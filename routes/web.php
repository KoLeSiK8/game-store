<?php

use App\Http\Controllers\AdminGameController;
use App\Http\Controllers\AdminPanelController;
use App\Http\Controllers\AdminReviewController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\GamePageController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserCatalogStateController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Каталог игр с фильтрами.
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');

// Страница игры.
Route::get('/games/{game}', [GamePageController::class, 'show'])->name('games.show');

// Страницы аутентификации.
Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');
Route::view('/profile', 'profile')->middleware('auth')->name('profile');

// Корзина.
Route::get('/cart', [CartController::class, 'view'])->name('cart.view');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
Route::post('/cart/toggle', [CartController::class, 'toggle'])->name('cart.toggle');

// Заказы, библиотека, отзывы, избранное и рекомендации.
Route::middleware(['auth'])->group(function () {
    Route::match(['get', 'post'], '/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::get('/orders', [OrderController::class, 'history'])->name('orders.history');
    Route::get('/orders/{order}', [OrderController::class, 'orderDetails'])->name('orders.details');
    Route::get('/library', [LibraryController::class, 'index'])->name('library.index');
    Route::get('/library/download/{gameFile}', [LibraryController::class, 'downloadGame'])->name('library.download');

    Route::get('/games/{game}/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/games/{game}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'delete'])->name('reviews.delete');

    Route::get('/wishlist', [WishlistController::class, 'list'])->name('wishlist.list');
    Route::post('/wishlist/add/{game}', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/remove/{game}', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/wishlist/toggle/{game}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

    Route::get('/recommendations', [RecommendationController::class, 'index'])->name('recommendations.index');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    Route::get('/catalog/state', [UserCatalogStateController::class, 'index'])->name('catalog.state');
});

// Админ: панель, модерация игр/отзывов и управление пользователями.
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
});

// Маршруты для продавцов.
Route::middleware(['auth', 'seller'])->group(function () {
    Route::get('/seller/games/create', [GameController::class, 'create'])->name('seller.games.create');
    Route::post('/seller/games', [GameController::class, 'store'])->name('seller.games.store');
    Route::get('/seller/games/{game}/edit', [GameController::class, 'edit'])->name('seller.games.edit');
    Route::put('/seller/games/{game}', [GameController::class, 'update'])->name('seller.games.update');
    Route::delete('/seller/games/{game}', [GameController::class, 'delete'])->name('seller.games.delete');
    Route::post('/seller/games/{game}/files', [GameController::class, 'uploadFile'])->name('seller.games.files.upload');
});

// Аутентификация.
Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');