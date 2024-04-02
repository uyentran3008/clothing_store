<?php

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CounponController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\OrderController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ProductController as ClientProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\PaymentController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('client.home');
Route::get('product/{category_id}', [ClientProductController::class, 'index'])->name('client.products.index');
Route::get('product-detail/{id}', [ClientProductController::class, 'show'])->name('client.products.show');

Route::middleware('auth')->group(function () {
    Route::post('add-to-cart', [CartController::class, 'store'])->name('client.carts.add');
    Route::get('carts', [CartController::class, 'index'])->name('client.carts.index');
    Route::post('update-quantity-product-in-cart/{cart_product_id}', [CartController::class, 'updateQuantityProduct'])->name('client.carts.update_product_quantity');
    Route::post('remove-product-in-cart/{cart_product_id}', [CartController::class, 'removeProductInCart'])->name('client.carts.remove_product');
    Route::post('apply-coupon', [CartController::class, 'applyCoupon'])->name('client.carts.apply_coupon');

    Route::get('checkout', [CartController::class, 'checkout'])->name('client.checkout.index')->middleware('user.can_checkout_cart');
    Route::post('process-checkout', [CartController::class, 'processCheckout'])->name('client.checkout.proccess')->middleware('user.can_checkout_cart');

    Route::post('vnpay_payment', [PaymentController::class, 'vnpay_payment'])->name('payment.vnpay_payment');

    Route::get('list-orders', [OrderController::class, 'index'])->name('client.orders.index');

    Route::post('orders/cancel/{id}', [OrderController::class, 'cancel'])->name('client.orders.cancel');


});


// Route::get('/product', function () {
//     return view('client.products.products');
// });
// Route::get('/detail', function () {
//     return view('client.products.detail');
// });
// Route::get('/home', function () {
//     return view('client.layouts.app');
// });

Auth::routes();
Route::middleware('auth')->group(function () {
    // Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::prefix('dashboard')->controller(DashboardController::class)->name('dashboard.')->group(function(){
        Route::get('/', 'index')->name('index')->middleware('permission:show-dashboard');
        
    });
    // Route::resource('roles', RoleController::class);
    // Route::resource('users', UserController::class);
    // Route::resource('categories', CategoryController::class);
    // Route::resource('products', ProductController::class);
    // Route::resource('coupons', CounponController::class);
  
    Route::prefix('roles')->controller(RoleController::class)->name('roles.')->group(function(){
        Route::get('/', 'index')->name('index')->middleware('role:super-admin');
        Route::post('/', 'store')->name('store')->middleware('role:super-admin');
        Route::get('/create', 'create')->name('create')->middleware('role:super-admin');
        Route::get('/{coupon}', 'show')->name('show')->middleware('role:super-admin');
        Route::put('/{coupon}', 'update')->name('update')->middleware('role:super-admin');
        Route::delete('/{coupon}', 'destroy')->name('destroy')->middleware('role:super-admin');
        Route::get('/{coupon}/edit', 'edit')->name('edit')->middleware('role:super-admin');
    });
    // Route::resource('users', UserController::class);
    Route::prefix('users')->controller(UserController::class)->name('users.')->group(function(){
        Route::get('/', 'index')->name('index')->middleware('permission:show-user');
        Route::post('/', 'store')->name('store');
        Route::get('/create', 'create')->name('create')->middleware('permission:create-user');
        Route::get('/{coupon}', 'show')->name('show')->middleware('permission:show-user');
        Route::put('/{coupon}', 'update')->name('update')->middleware('permission:update-user');
        Route::delete('/{coupon}', 'destroy')->name('destroy')->middleware('permission:delete-user');
        Route::get('/{coupon}/edit', 'edit')->name('edit')->middleware('permission:update-user');
    });
    // Route::resource('categories', CategoryController::class);
    Route::prefix('categories')->controller(CategoryController::class)->name('categories.')->group(function(){
        Route::get('/', 'index')->name('index')->middleware('permission:show-category');
        Route::post('/', 'store')->name('store')->middleware('permission:create-category');
        Route::get('/create', 'create')->name('create')->middleware('permission:create-category');
        Route::get('/{coupon}', 'show')->name('show')->middleware('permission:show-category');
        Route::put('/{coupon}', 'update')->name('update')->middleware('permission:update-category');
        Route::delete('/{coupon}', 'destroy')->name('destroy')->middleware('permission:delete-category');
        Route::get('/{coupon}/edit', 'edit')->name('edit')->middleware('permission:update-category');
    });

    // Route::resource('products', ProductController::class);

    Route::prefix('products')->controller(ProductController::class)->name('products.')->group(function(){
        Route::get('/', 'index')->name('index')->middleware('permission:show-product');
        Route::post('/', 'store')->name('store')->middleware('permission:create-product');
        Route::get('/create', 'create')->name('create')->middleware('permission:create-product');
        Route::get('/{coupon}', 'show')->name('show')->middleware('permission:show-product');
        Route::put('/{coupon}', 'update')->name('update')->middleware('permission:update-product');
        Route::delete('/{coupon}', 'destroy')->name('destroy')->middleware('permission:delete-product');
        Route::get('/{coupon}/edit', 'edit')->name('edit')->middleware('permission:update-product');
    });



    Route::prefix('coupons')->controller(CounponController::class)->name('coupons.')->group(function(){
            Route::get('/', 'index')->name('index')->middleware('permission:show-coupon');
            Route::post('/', 'store')->name('store')->middleware('permission:create-coupon');
            Route::get('/create', 'create')->name('create')->middleware('permission:create-coupon');
            Route::get('/{coupon}', 'show')->name('show')->middleware('permission:show-coupon');
            Route::put('/{coupon}', 'update')->name('update')->middleware('permission:update-coupon');
            Route::delete('/{coupon}', 'destroy')->name('destroy')->middleware('permission:delete-coupon');
            Route::get('/{coupon}/edit', 'edit')->name('edit')->middleware('permission:update-coupon');
    });

    // Route::resource('warehouse', WarehouseController::class);
    Route::prefix('warehouse')->controller(WarehouseController::class)->name('warehouse.')->group(function(){
        Route::get('/', 'index')->name('index')->middleware('permission:show-warehouse');
        
    });
    // Route::resource('materials',MaterialController::class);
    Route::prefix('materials')->controller(MaterialController::class)->name('materials.')->group(function(){
        Route::get('/', 'index')->name('index')->middleware('permission:show-material');
        Route::post('/', 'store')->name('store')->middleware('permission:create-material');
        Route::get('/create', 'create')->name('create')->middleware('permission:create-material');
        Route::get('/{material}', 'show')->name('show')->middleware('permission:show-material');
        Route::put('/{material}', 'update')->name('update')->middleware('permission:update-material');
        Route::delete('/{material}', 'destroy')->name('destroy')->middleware('permission:delete-material');
        Route::get('/{material}/edit', 'edit')->name('edit')->middleware('permission:update-material');
});


    Route::get('orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::post('update-status/{id}', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.update_status');
    Route::get('/order/{id}',  [AdminOrderController::class, 'show'])->name('admin.orders.show');
    


});

// Route::post('/vnpay_payment', [PaymentController::class, 'vnpay_payment']);

