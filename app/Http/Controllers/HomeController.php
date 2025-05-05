<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Product;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    /*   public function __construct()
      {
          $this->middleware('auth');
      } */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $slides = Slider::where('status', 1)->get()->take(3);

        $categories = Category::orderBy('name')->get();
        //dd();
        $sproducts = Product::whereNotNull('sale_price')
            ->where('sale_price', '!=', '')
            ->inRandomOrder()
            // تصحيح: يجب أن يكون قبل get()
            ->get()->take(8);

        $fproducts = Product::where('featured', 1)->take(8)->get();
        $productcats = Category::whereHas('products', function ($query) {
            $query->where('sale_price', '<=', 190);
        })->with([
                    'products' => function ($query) {
                        $query->where('sale_price', '<=', 190)
                            ->orderBy('sale_price', 'asc')
                            ->take(2); // جلب منتج واحد لكل فئة
                    }
                ])->get()->take(2);
        return view('index', compact('slides', 'categories', 'sproducts', 'fproducts', 'productcats'));
    }

    public function contact()
    {
        return view('contact');
    }

    public function contact_store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in first');
        }
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email',
            'phone' => 'required|numeric|digits:10 ',
            'comment' => 'required'
        ]);
        $contact = new Contact();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->comment = $request->comment;
        $contact->save();
        return redirect()->back()->with('success', 'Your message has been sent successfully!');

    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $result = Product::where('name', 'LIKE', "%$query%")
            ->orWhere('short_description', 'LIKE', "%$query%")
            ->get();
        return response()->json($result);
    }
    public function about()
    {
        return view('about');
    }
}