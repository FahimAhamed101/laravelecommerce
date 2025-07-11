<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Session\SessionManager;
use Illuminate\Database\DatabaseManager;
use Illuminate\Contracts\Events\Dispatcher;
use Surfsidemedia\Shoppingcart\Contracts\Buyable;
use Surfsidemedia\Shoppingcart\Exceptions\UnknownModelException;
use Surfsidemedia\Shoppingcart\Exceptions\InvalidRowIDException;
use Surfsidemedia\Shoppingcart\Exceptions\CartAlreadyStoredException;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();
        return view('cart', compact('items'));
    }

    public function add_to_cart(Request $request)
    {
        // $productuu=new Product();{{ number_format($order->subtotal, 2) }} for display
        Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price)->associate((string) "App\Models\Product");
        return redirect()->back();
    }

    public function increase_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }


    public function decrease_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();

    }

    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();

    }

    public function apply_coupon_code(Request $request)
    {
        $coupon_code = $request->coupon_code;
        if (isset($coupon_code)) {
            // Fix the expiry date comparison (should be >= today)
            $coupon = Coupon::where('code', $coupon_code)
                ->where('expiry_date', '>=', Carbon::today()) // Changed from <= to >=
                ->where('cart_value', '<=', (float)str_replace(',', '', Cart::instance('cart')->subtotal()))
                ->first();
    
            if (!$coupon) {
                return redirect()->back()->with('error', 'Invalid coupon code!');
            }
    
            Session::put('coupon', [
                'code' => $coupon->code,
                'type' => $coupon->type,
                'value' => $coupon->value,
                'cart_value' => $coupon->cart_value
            ]);
            
            $this->calculateDiscount();
            return redirect()->back()->with('success', 'Coupon has been applied!');
        }
    
        return redirect()->back()->with('error', 'Invalid coupon code!');
    }
    public function calculateDiscount()
    {
        $discount = 0;
        if (Session::has('coupon')) {
            // Get raw subtotal without formatting
            $rawSubtotal = (float)str_replace(',', '', Cart::instance('cart')->subtotal());
            
            if (Session::get('coupon')['type'] == 'fixed') {
                $discount = (float)Session::get('coupon')['value'];
            } else {
                $discount = ($rawSubtotal * (float)Session::get('coupon')['value']) / 100;
            }
            
            $subtotalAfterDiscount = $rawSubtotal - $discount;
            $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax')) / 100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;
    
            // Store raw numbers without formatting
            Session::put('discounts', [
                'discount' => $discount,
                'subtotal' => $subtotalAfterDiscount,
                'tax' => $taxAfterDiscount,
                'total' => $totalAfterDiscount,
            ]);
        }
    }
    public function remove_coupon_code()
    {
        Session::forget('coupon');
        Session::forget('discounts');
        return redirect()->back()->with('success', 'Coupon has been removed');

    }

    public function checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in first');
        }
        $address = Address::where('user_id', Auth::user()->id)->where('isdefault', 1)->first();
        return view('checkout', compact('address'));
    }

    public function place_on_order(Request $request)
    {
        $user_id = optional(Auth::user())->id;
        // dd($request->all()); // تأكد من وصول البيانات قبل أي عمليات أخرى

        $address = Address::where('user_id', $user_id)->where('isdefault', true)->first();
        if (!$address) {
            $request->validate([
                'name' => 'required|max:100',
                'phone' => 'required|numeric|digits:10',
                'address' => 'required',
                'zip' => 'required|numeric|digits:6',
                'city' => 'required',
                'landmark' => 'required',
                'locality' => 'required',
                'state' => 'required'
            ]);
            $address = new Address();
            $address->name = $request->name;
            $address->phone = $request->phone;
            $address->address = $request->address;
            $address->zip = $request->zip;
            $address->city = $request->city;
            $address->landmark = $request->landmark;
            $address->locality = $request->locality;
            $address->state = $request->state;
            $address->country = 'Syria';
            $address->user_id = $user_id;
            $address->isdefault = true;
            $address->save();
        }
        $this->setAmountforCheckout();

        $order = new Order();
        //dd($order);
        $order->user_id = $user_id;
        //  dd(Session::get('checkout'));
        //dd($order);

        $order->subtotal = Session::get('checkout')['subtotal'];
        $order->discount = Session::get('checkout')['discount'];
        $order->tax = Session::get('checkout')['tax'];
        $order->total = Session::get('checkout')['total'];
        $order->name = $address->name;
        $order->locality = $address->locality;
        $order->address = $address->address;
        $order->city = $address->city;
        $order->state = $address->state;
        $order->country = $address->country;
        $order->landmark = $address->landmark;
        $order->zip = $address->zip;
        $order->phone = $address->phone;
        $order->save();

        foreach (Cart::instance('cart')->content() as $item) {
            $orderitem = new OrderItem();
            $orderitem->product_id = $item->id;
            $orderitem->order_id = $order->id;
            $orderitem->price = $item->price;
            $orderitem->quantity = $item->qty;
            $orderitem->save();
        }

        if ($request->mode == "card") {
            $transaction = new Transaction();
            $transaction->user_id = $user_id;
            $transaction->order_id = $order->id;
            $transaction->mode = $request->mode;
            $transaction->status = 'pending';
            $transaction->save();
        } else if ($request->mode == "paypal") {
            $transaction = new Transaction();
            $transaction->user_id = $user_id;
            $transaction->order_id = $order->id;
            $transaction->mode = $request->mode;
            $transaction->status = 'pending';
            $transaction->save();
        } else if ($request->mode == "cod") {
            $transaction = new Transaction();
            $transaction->user_id = $user_id;
            $transaction->order_id = $order->id;
            $transaction->mode = $request->mode;
            $transaction->status = 'pending';
            $transaction->save();
        }

        Cart::instance('cart')->destroy();
        Session::forget('checkout');
        Session::forget('coupon');
        Session::forget('discounts');
        Session::put('order_id', $order->id);
        // return view('order-confirmation',compact('order'));
        return redirect()->route('cart.order.confirmation');
    }
    public function setAmountforCheckout()
    {
        if (!Cart::instance('cart')->content()->count() > 0) {
            Session::forget('checkout');
            return;
        }
        
        if (Session::has('coupon')) {
            Session::put('checkout', [
                'discount' => Session::get('discounts')['discount'],
                'subtotal' => Session::get('discounts')['subtotal'],
                'tax' => Session::get('discounts')['tax'],
                'total' => Session::get('discounts')['total'],
            ]);
        } else {
            // Get raw values without formatting
            $subtotal = (float)str_replace(',', '', Cart::instance('cart')->subtotal());
            $tax = (float)str_replace(',', '', Cart::instance('cart')->tax());
            $total = (float)str_replace(',', '', Cart::instance('cart')->total());
            
            Session::put('checkout', [
                'discount' => 0,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);
        }
    }

    public function order_confirmation()
    {
        if (Session::has('order_id')) {
            $order = Order::find(Session::get('order_id'));
            return view('order-confirmation', compact('order'));
        }
        return redirect()->route('cart.index');
    }

}
