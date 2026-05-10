<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\ImportController;
use App\Http\Controllers\Admin\LeaderboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\MainController as MainAdminController;
use App\Http\Controllers\Affiliator\MainController as MainAffiliateController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/test-email', function () {
    Mail::raw('Halo, ini test SMTP Hostinger dari Laravel 12!', function ($message) {
        $message->to('ridwansetiobudi77@gmail.com')
                ->subject('Test SMTP Hostinger');
    });
    return 'Email terkirim!';
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showUsername'])->name('login');
    Route::post('/verify-username', [AuthController::class, 'verifyUsername'])->name('login.verify-username');
    
    Route::get('/check-password',[AuthController::class,'showPassword'])->name('login.password');
    Route::post('/verify-password',[AuthController::class,'verifyPassword'])->name('login.verify-password');
    
    Route::get('/request-access',[AuthController::class,'showAccessRequestForm'])->name('access.request');
    Route::post('/request-access',[AuthController::class,'submitAccessRequest'])->name('access.send-request');
    
    Route::get('/claim', [AuthController::class,'showFormClaim'])->name('account.claim');
    Route::post('/claim',[AuthController::class,'claim'])->name('account.send-claim');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Link verifikasi telah dikirim ulang!');
    })->middleware(['throttle:6,1'])->name('verification.send');

    Route::middleware(['role:administrator'])->prefix('dashboard')->group(function () {
        Route::get('/', [MainAdminController::class, 'index'])->name('dashboard');

        Route::get('/import-data', [ImportController::class, 'getImportData'])->name('admin-dashboard.import');
        Route::get('/import-data/data', [ImportController::class, 'data'])->name('admin-dashboard.import-data');
        Route::post('/import-data', [ImportController::class, 'importData'])->name('admin-dashboard.store');
        
        Route::get('/product',[ProductController::class, 'index'])->name('admin-dashboard.product-index');
        Route::get('/product/data',[ProductController::class, 'data'])->name('admin-dashboard.product-data');
        Route::post('/import-product-update', [ProductController::class, 'importData'])->name('admin-dashboard.import-product-update');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('admin-dashboard.product-update');
        Route::post('/products/mass-update', [ProductController::class, 'massUpdate'])->name('admin-dashboard.product-mass-update');
        
        Route::post('/import-data', [ImportController::class, 'importData'])->name('request.access'); /// ! 

        Route::get('/analytics', [AnalyticsController::class, 'index'])->name('admin-dashboard.analytics');
        Route::get('/analytics/detail-roi-data', [AnalyticsController::class, 'detailRoiData'])->name('admin-dashboard.analytics.detail-roi-data');
        Route::get('/leaderboard',[LeaderboardController::class,'index'])->name('admin-dashboard.leaderboard');

        Route::get('/users', [UserController::class, 'index'])->name('admin-dashboard.users.index');
        Route::get('/users/data/active', [UserController::class, 'activeData'])->name('admin-dashboard.users.active-data');
        Route::get('/users/data/request-access', [UserController::class, 'requestAccessData'])->name('admin-dashboard.users.request-access-data');
        Route::get('/users/data/blacklist', [UserController::class, 'blacklistData'])->name('admin-dashboard.users.blacklist-data');
        Route::get('/users/data/kol-contract', [UserController::class, 'kolContractData'])->name('admin-dashboard.users.kol-contract-data');
        Route::post('/users/approve-access', [UserController::class, 'approveAccess'])->name('admin-dashboard.users.approve-access');
        Route::post('/users/reject-access', [UserController::class, 'rejectAccess'])->name('admin-dashboard.users.reject-access');
        Route::post('/users/store-blacklist', [UserController::class, 'storeBlacklist'])->name('admin-dashboard.users.store-blacklist');
        Route::post('/users/restore-blacklist', [UserController::class, 'restoreBlacklist'])->name('admin-dashboard.users.restore-blacklist');
        Route::post('/users/extend-kol-contract', [UserController::class, 'extendKOLContract'])->name('admin-dashboard.users.extend-kol-contract');
        Route::post('/users/store-kol-contract', [UserController::class, 'storeKOLContract'])->name('admin-dashboard.users.store-kol-contract');
    
        });
    Route::middleware(['role:affiliator'])->prefix('affiliator')->group(function () {
        Route::get('/', [MainAffiliateController::class,'index']);
    });

});