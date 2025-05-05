<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PurchaseProductController;

// Route::get('/', function () {
//     return Inertia::render('frontend/Index', [
//         'title' => 'Home',
//         'description' => 'Welcome to our website!',
//         'translations' => __('messages'),
//         'locale' => App::getLocale(),
//     ]);
// })->name('home');
Route::get('/', [MainController::class, 'index'])->name('home');
Route::get('/product/{slug}', [MainController::class, 'productDetail'])->name('product.detail');
Route::get('/lang/{locale}', [MainController::class, 'switchLanguage'])->name('language.switch');

Route::post('/cart/add', [MainController::class, 'addToCart'])->name('cart.add');


Route::middleware(['auth', 'admin', 'verified'])->name('admin.')->group(function () {
    Route::get('cache-clear', [MainController::class, 'cacheClear'])->name('cache.clear');
    Route::get('admin/dashboard', [MainController::class, 'dashboard'])->name('dashboard');
    Route::get('categories', [CategoryController::class, 'index'])->name('categories');
    Route::post('category/store', [CategoryController::class, 'store'])->name('category.store');
    Route::put('category/update/{id}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('category/delete/{id}', [CategoryController::class, 'destroy'])->name('category.delete');
    Route::get('category/log', [CategoryController::class, 'category_log'])->name('category.log');
    Route::get('brands', [BrandController::class, 'index'])->name('brands');
    Route::post('brand/store', action: [BrandController::class, 'store'])->name('brand.store');
    Route::get('brand/list/{slug}', action: [BrandController::class, 'related_brand_list'])->name('related-brand-list');
    Route::delete('brand/delete/{id}', [BrandController::class, 'destroy'])->name('brand.delete');
    Route::put('brand/update/{id}', [BrandController::class, 'update'])->name('brand.update');
    Route::get('brand/logs', [BrandController::class, 'brand_log'])->name('brand-log');
    Route::get('brand/edit/{id}', [BrandController::class, 'edit'])->name('brand.edit');
    Route::get('products', [ProductController::class, 'index'])->name('products');
    Route::post('product/store', action: [ProductController::class, 'store'])->name('product.store');
    Route::get('product/list/{slug}', action: [ProductController::class, 'related_product_list'])->name('related-product-list');
    Route::delete('product/delete/{id}', [ProductController::class, 'destroy'])->name('product.delete');
    Route::post('product/update/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::get('product/logs', [ProductController::class, 'product_log'])->name('product-log');

    Route::post('purchase-product-detail/store', [PurchaseProductController::class, 'store'])->name('purchase.product.detail.store');
    Route::get('related-purchase-products/list/{slug}', [PurchaseProductController::class, 'relatedPurchaseProductList'])->name('related.purchase.product.list');
    Route::delete('purchase-product-delete/{id}', [PurchaseProductController::class, 'destroy'])->name('purchase.product.delete');
    Route::put('purchase-product-update/{id}', [PurchaseProductController::class, 'update'])->name('purchase.product.update');
    Route::get('purchase/products/list', [PurchaseProductController::class, 'purchaseProductList'])->name('purchase.product.list');
    Route::get('purchase/product/logs', [PurchaseProductController::class, 'purchaseProductLog'])->name('purchase.product.log');
    Route::get('order/create', [OrderController::class, 'orderCreate'])->name('order.create');
    Route::get('order/list', [OrderController::class, 'orderList'])->name('order.list');
    Route::post('order/store', [OrderController::class, 'store'])->name('order.store');
    Route::get('/admin/get-brands-by-category/{category}', [ProductController::class, 'getBrandsByCategory'])->name('admin.get.brands.by.category');
});


Route::get('lacuna-migrate', [MainController::class, 'migrate'])->name('migrate');
Route::get('storage-link', [MainController::class, 'storageLink'])->name('storage.link');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [MainController::class, 'checkRole'])->name('dashboard');
});


require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
