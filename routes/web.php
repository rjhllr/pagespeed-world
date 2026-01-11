<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Marketing\ContactController;
use App\Http\Controllers\Marketing\MarketingBlogController;
use App\Http\Controllers\Marketing\MarketingPageController;
use App\Http\Controllers\Marketing\SitemapController;
use App\Http\Controllers\ScreenshotController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::redirect('/', '/en');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');

// Dashboard routes (authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/pages/{page}', [DashboardController::class, 'show'])->name('dashboard.page');
    Route::get('/dashboard/account', [DashboardController::class, 'account'])->name('dashboard.account');
    Route::get('/api/pages/{page}/metrics', [DashboardController::class, 'pageMetrics']);
    
    // Screenshot proxy (serves private bucket files through authenticated controller)
    Route::get('/screenshots/{bundleSize}/{filename}', [ScreenshotController::class, 'show'])
        ->name('screenshots.show')
        ->where('filename', '.*');
});

// Marketing site (localized)
Route::prefix('{locale}')
    ->whereIn('locale', ['en', 'de'])
    ->group(function () {
        Route::get('/', [MarketingPageController::class, 'home'])->name('marketing.home');
        Route::get('/features', [MarketingPageController::class, 'features'])->name('marketing.features');
        Route::get('/pricing', [MarketingPageController::class, 'pricing'])->name('marketing.pricing');
        Route::get('/faq', [MarketingPageController::class, 'faq'])->name('marketing.faq');
        Route::get('/about', [MarketingPageController::class, 'about'])->name('marketing.about');
        Route::get('/contact', [MarketingPageController::class, 'contact'])->name('marketing.contact');
        Route::post('/contact', [ContactController::class, 'submit'])->name('marketing.contact.submit');

        Route::get('/blog', [MarketingBlogController::class, 'index'])->name('marketing.blog.index');
        Route::get('/blog/{slug}', [MarketingBlogController::class, 'show'])->name('marketing.blog.show');
    });
