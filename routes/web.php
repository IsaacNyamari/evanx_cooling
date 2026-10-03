<?php

use App\Models\Category;
use App\Models\Product;
use App\Http\Controllers\ContactMessagesController;
use App\Http\Controllers\ContactPageController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\Sitemap;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/contact/', function () {
    return view('contact');
})->name('contact');

Route::get('/services/', function () {
    return view('services');
})->name('services');

Route::get('/about-us/', function () {
    return view('about');
})->name('about');

Route::get('/services/ac-installation-by-evanx-cooling-systems/', function () {
    return view('services.ac-installation');
})->name('ac.installation');

Route::get('/services/cooling-services-by-evanx-cooling-systems/', function () {
    return view('services.cooling-services');
})->name('ac.cooling');

Route::get('/services/heating-services-by-evanx-cooling-systems/', function () {
    return view('services.heating-services');
})->name('heating.services');

Route::get('/services/indoor-air-quality-by-evanx-cooling-systems/', function () {
    return view('services.indoor-air-quality');
})->name('indoor.air.quality');

Route::get('/services/maintenance-and-repair-by-evanx-cooling-systems/', function () {
    return view('services.maintenance-repair');
})->name('maintenace.and.repair');

Route::get('/services/hvac-annual-inspection-at-evanx-cooling-systems/', function () {
    return view('services.annual-inspections');
})->name('annual.inspection');

Route::get('/shop', [ShopController::class, 'index'])->name('shop');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::view('categories', 'admin.categories.index')->name('categories.index');
    Route::get('categories/create', fn () => view('admin.categories.form', ['category' => new Category]))->name('categories.create');
    Route::get('categories/{category}/edit', fn (Category $category) => view('admin.categories.form', compact('category')))->name('categories.edit');

    Route::get('seo', fn () => view('admin.seo', ['tab' => 'overview']))->name('seo');
    Route::get('seo/pages', fn () => view('admin.seo', ['tab' => 'pages']))->name('seo.pages');
    Route::get('seo/products', fn () => view('admin.seo', ['tab' => 'products']))->name('seo.products');

    Route::view('sitemap', 'admin.sitemap')->name('sitemap');
    Route::get('sitemap/download', [SitemapController::class, 'download'])->name('sitemap.download');

    Route::view('deployments', 'admin.deployments')->name('deployments');

    Route::view('products', 'admin.products.index')->name('products.index');
    Route::get('products/create', fn () => view('admin.products.form', ['product' => new Product]))->name('products.create');
    Route::get('products/{product}/edit', fn (Product $product) => view('admin.products.form', compact('product')))->name('products.edit');
});
Route::post('/send-message/', [ContactPageController::class, 'send'])->name('send.message');

// Sitemap routes - serve dynamically and generate
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/robots.txt', [SitemapController::class, 'robots']);

Route::get('/admin', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::resource('/admin/messages/', MessageController::class);
Route::get('/admin/messages/', [MessageController::class, 'index'])->name('messages');
Route::post('/admin/messages/{id}', [MessageController::class, 'destroy'])->name('admin.messages.destroy');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::post('/logout', function () {
    Auth::logout();

    return redirect()->route('login');
})->name('logout');

require __DIR__.'/auth.php';