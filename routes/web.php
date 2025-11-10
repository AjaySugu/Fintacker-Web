<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Auth\LoginController;
use App\Http\Controllers\V1\Banking\WebPushController;
use App\Http\Controllers\FirebaseController;
use Illuminate\Support\Facades\Artisan;

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

// Cache clear
Route::get('/optimize-clear', function () {
    Artisan::call('optimize:clear');
    Artisan::call('migrate');
    return 'Application optimized cache cleared successfully!';
});

// URL's
Route::get('/login', [LoginController::class, 'index'])->name('login'); 

Route::prefix('auth')->group(function () {
    Route::post('/login-with-otp', [LoginController::class, 'sendOtp']);
    Route::post('/verify-otp', [LoginController::class, 'verifyOtp']);
});


Route::post('/app-save-fcm-token', [FirebaseController::class, 'saveTokenApp']);
Route::post('/send-test-notification', [FirebaseController::class, 'sendNotificationApp']);











Route::middleware(['auth'])->group(function () {

    Route::post('/check-notif', [WebPushController::class, 'check']);
Route::post('/save-subscription', [WebPushController::class, 'store']);

     Route::post('/store-push', [WebPushController::class, 'store']);
});

 // Save FCM token
    Route::post('/save-fcm-token', [FirebaseController::class, 'storeToken'])->name('fcm.store');

    // Optional: test notification
    Route::post('/send-notification', [FirebaseController::class, 'sendNotification'])->name('fcm.test');

Route::get('/check-user', function() {
    return auth()->user();
})->middleware('auth');
