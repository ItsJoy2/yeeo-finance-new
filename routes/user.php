<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\user\AuthController;
use App\Http\Controllers\user\UserController;
use App\Http\Controllers\user\DepositController;
use App\Http\Controllers\user\PackagesController;
use App\Http\Controllers\user\DashboardController;
use App\Http\Controllers\user\TransactionsController;

Route::prefix('user')->middleware('auth')->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('user.dashboard');
    Route::post('email/verification-notification',[EmailController::class,'sendVerificationEmail']);
    Route::get('verify-email/{id}/{hash}',[EmailController::class,'verify'])->middleware(['signed'])->name('verification.verify');

    Route::post('logout', [AuthController::class, 'logout'])->name('user.logout');

    // activation
    Route::get('activation', [UserController::class, 'showActivation'])->name('user.activation');
    Route::post('account/activate', [UserController::class, 'activeAccount'])->name('user.account.activate');

    // package
    Route::get('packages', [PackagesController::class, 'index'])->name('user.packages');
    Route::post('buy-package', [PackagesController::class, 'buyPackage'])->name('user.packages.buy');
    Route::get('investment-history', [PackagesController::class, 'InvestHistory'])->name('user.Investment.history');
        //deposit
    Route::resource('deposit', DepositController::class)->only(['index', 'store']) ->names([
        'index' => 'user.deposit.index',
        'store' => 'user.deposit.store',
    ]);
     Route::get('deposit/invoice/{invoice_id}', [DepositController::class, 'showInvoice'])->name('user.deposit.invoice');
     Route::get('deposit/history', [DepositController::class, 'history'])->name('user.deposit.history');



    // Transactions
    Route::get('transactions', [TransactionsController::class, 'transactions'])->name('user.transactions');



});
