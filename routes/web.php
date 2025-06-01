<?php
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Middleware\AuthAdmin;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ShopController;
use Surfsidemedia\Shoppingcart\Facades\Cart;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
Auth::routes();

Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product_slug}', [ShopController::class, 'product_details'])->name('shop.product.details');
//CART
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add_to_cart'])->name('cart.add');
Route::any('/cart/increasequantity/{rowId}', [CartController::class, 'increase_cart_quantity'])->name('cart.qty.increase');//put
Route::any('/cart/decreasequantity/{rowId}', [CartController::class, 'decrease_cart_quantity'])->name('cart.qty.decrease');//put
Route::delete('/cart/remove_item/{rowId}', [CartController::class, 'remove_item'])->name('cart.item.remove');
Route::delete('/cart/clear', [CartController::class, 'empty_cart'])->name('cart.empty');
Route::post('/cart/apply-coupon', [CartController::class, 'apply_coupon_code'])->name('cart.coupon.apply');
Route::delete('/cart/delete-coupon', [CartController::class, 'remove_coupon_code'])->name('cart.coupon.remove');
//WISHLIST
Route::post('/wishlist/add', [WishlistController::class, 'add_to_wishlist'])->name('wishlist.add');
Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::delete('/wishlist/remove_item/{rowId}', [WishlistController::class, 'remove_item'])->name('wishlist.item.remove');
Route::delete('/wishlist/clear', [WishlistController::class, 'empty_wishlist'])->name('wishlist.empty');
Route::post('/wishlist/move-to-cart/{rowId}', [WishlistController::class, 'move_to_cart'])->name('wishlist.to.cart');
Route::get('/wishlist/count', function () {
    return response()->json(['count' => Cart::instance('wishlist')->count()]);
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::middleware(['auth'])->group(function () {
    Route::get('/account-dashboard', [UserController::class, 'index'])->name('user.index');
   
});
Route::middleware(['auth', AuthAdmin::class])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/admin/product/add', [AdminController::class, 'product_add'])->name('admin.product.add');
    Route::any('/admin/product/store', [AdminController::class, 'products_store'])->name('admin.product.store');
    Route::get('/admin/product/{id}/edit', [AdminController::class, 'product_edit'])->name('admin.product.edit');
    Route::put('/admin/product/update', [AdminController::class, 'product_update'])->name('admin.product.update');
    Route::delete('/admin/product/{id}/delete', [AdminController::class, 'product_delete'])->name('admin.product.delete');
});    
