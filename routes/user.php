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

    // withdraw

    Route::get('withdraw', [TransactionsController::class, 'showWithdrawForm'])->name('user.withdraw.index');
    Route::post('withdraw', [TransactionsController::class, 'withdraw'])->name('user.withdraw.submit');

       //transfer

    Route::get('/transfer', [TransactionsController::class, 'showTransferForm'])->name('user.transfer.form');
    Route::post('/transfer', [TransactionsController::class, 'transfer'])->name('user.transfer.submit');

    // Transactions
    Route::get('transactions', [TransactionsController::class, 'transactions'])->name('user.transactions');


    //profile
    Route::get('profile', [AuthController::class, 'profileEdit'])->name('user.profile');
    Route::post('profile', [AuthController::class, 'updateProfile'])->name('user.profile.update');
    Route::post('change-password', [AuthController::class, 'changePassword'])->name('user.changePassword');
    Route::get('my-referrals', [UserController::class, 'directReferrals'])->name('user.direct.referrals');




});
