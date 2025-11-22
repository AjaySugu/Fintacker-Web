<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Banking\BudgetController;
use App\Http\Controllers\V1\Banking\WebPushController;
use App\Http\Controllers\V1\Banking\SubscriptionController;
use App\Http\Controllers\V1\Banking\TransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\V1\Banking\CategoryController;

Route::post('/app-save-fcm-token', [FirebaseController::class, 'saveTokenApp']);
Route::post('/send-test-notification', [FirebaseController::class, 'sendNotificationApp']);

 // ✅ Get all active subscriptions for a user
    Route::get('subscriptions/{userId}', [SubscriptionController::class, 'index']);

    // ✅ Get total + type-wise insights
    Route::get('subscriptions/{userId}/insights', [SubscriptionController::class, 'insights']);

    // ✅ Send smart reminders (can be scheduled)
    Route::post('subscriptions/send-reminders', [SubscriptionController::class, 'sendReminders']);


    Route::get('/transactions/{userId}', [TransactionController::class, 'index']);
Route::post('/transactions', [TransactionController::class, 'store']);
Route::get('/transactions/summary/{userId}', [TransactionController::class, 'summary']);
Route::delete('/transactions/{id}', [TransactionController::class, 'destroy']);

Route::get('/get-user-budgets', [BudgetController::class, 'getUserBudgets']);

Route::post('/store-icon', [CategoryController::class, 'storeIcon']);

