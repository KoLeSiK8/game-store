<?php

use App\Http\Controllers\AdminGameController;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\AdminReviewController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GamePageController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SellerDashboardController;
use App\Http\Controllers\SellerRequestController;
use App\Http\Controllers\UserCatalogStateController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/games/{game}', [GamePageController::class, 'show'])->name('games.show');

Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::view('/profile', 'profile')->middleware('auth')->name('profile');

Route::get('/cart', [CartController::class, 'view'])->name('cart.view');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/toggle', [CartController::class, 'toggle'])->name('cart.toggle');

Route::middleware(['auth'])->group(function () {
    Route::post('/orders/create', [CheckoutController::class, 'createOrder'])->name('orders.create');
    Route::get('/checkout/{order}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/{order}/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');
    Route::get('/orders/{order}/success', [CheckoutController::class, 'success'])->name('orders.success');
    Route::get('/orders/{order}/failed', [CheckoutController::class, 'failed'])->name('orders.failed');

    Route::get('/orders', [OrderController::class, 'history'])->name('orders.history');
    Route::get('/orders/{order}', [OrderController::class, 'orderDetails'])->name('orders.details');

    Route::get('/library', [LibraryController::class, 'index'])->name('library.index');
    Route::get('/library/files/{gameFile}', [DownloadController::class, 'show'])->name('library.files.show');
    Route::get('/library/download/{gameFile}', [DownloadController::class, 'download'])->name('library.download');

    Route::get('/games/{game}/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/games/{game}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'delete'])->name('reviews.delete');

    Route::get('/wishlist', [WishlistController::class, 'list'])->name('wishlist.list');
    Route::post('/wishlist/add/{game}', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/remove/{game}', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/wishlist/toggle/{game}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::get('/seller/request', [SellerRequestController::class, 'create'])->name('seller.request.create');
    Route::post('/seller/request', [SellerRequestController::class, 'submitRequest'])->name('seller.request.submit');

    Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/catalog/state', [UserCatalogStateController::class, 'index'])->name('catalog.state');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminPanelController::class, 'index'])->name('admin.index');

    Route::get('/admin/games', [AdminGameController::class, 'index'])->name('admin.games.index');
    Route::post('/admin/games/{game}/approve', [AdminGameController::class, 'approveGame'])->name('admin.games.approve');
    Route::post('/admin/games/{game}/reject', [AdminGameController::class, 'rejectGame'])->name('admin.games.reject');

    Route::get('/admin/reviews', [AdminReviewController::class, 'index'])->name('admin.reviews.index');
    Route::post('/admin/reviews/{review}/approve', [AdminReviewController::class, 'approveReview'])->name('admin.reviews.approve');
    Route::delete('/admin/reviews/{review}', [AdminReviewController::class, 'deleteReview'])->name('admin.reviews.delete');

    Route::get('/admin/users', [AdminUserController::class, 'listUsers'])->name('admin.users.index');
    Route::post('/admin/users/{user}/ban', [AdminUserController::class, 'banUser'])->name('admin.users.ban');
    Route::post('/admin/users/{user}/role', [AdminUserController::class, 'assignRole'])->name('admin.users.role');

    Route::get('/admin/seller-requests', [SellerRequestController::class, 'index'])->name('admin.seller_requests.index');
    Route::post('/admin/seller-requests/{sellerRequest}/approve', [SellerRequestController::class, 'approveRequest'])->name('admin.seller_requests.approve');
    Route::post('/admin/seller-requests/{sellerRequest}/reject', [SellerRequestController::class, 'rejectRequest'])->name('admin.seller_requests.reject');
});

Route::middleware(['auth', 'seller'])->group(function () {
    Route::get('/seller/dashboard', [SellerDashboardController::class, 'index'])->name('seller.dashboard');
    Route::get('/seller/games/create', [GameController::class, 'create'])->name('seller.games.create');
    Route::post('/seller/games', [GameController::class, 'store'])->name('seller.games.store');
    Route::get('/seller/games/{game}/edit', [GameController::class, 'edit'])->name('seller.games.edit');
    Route::put('/seller/games/{game}', [GameController::class, 'update'])->name('seller.games.update');
    Route::delete('/seller/games/{game}', [GameController::class, 'delete'])->name('seller.games.delete');
    Route::post('/seller/games/{game}/files', [GameController::class, 'uploadFile'])->name('seller.games.files.upload');
    Route::post('/seller/games/{game}/files/{gameFile}/activate', [GameController::class, 'activateFile'])->name('seller.games.files.activate');
    Route::delete('/seller/games/{game}/files/{gameFile}', [GameController::class, 'deleteFile'])->name('seller.games.files.delete');
});

Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');