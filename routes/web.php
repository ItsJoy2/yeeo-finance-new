<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CronController;
use App\Http\Controllers\admin\KycController;
use App\Http\Controllers\admin\PlansController;
use App\Http\Controllers\admin\UsersController;
use App\Http\Controllers\admin\DepositController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\WithdrawController;
use App\Http\Controllers\admin\AdminClubController;
use App\Http\Controllers\admin\AdminTicketController;
use App\Http\Controllers\admin\TransactionsController;
use App\Http\Controllers\admin\AdminDashboardController;
use App\Http\Controllers\admin\GeneralSettingsController;
use App\Http\Controllers\admin\WithdrawSettingsController;
use App\Http\Controllers\admin\ReferralsSettingsController;

Route::get('/', function () {
    return redirect()->route('user.dashboard');
});

Route::get('admin/dashboard',[AdminDashboardController::class,'index'])->middleware(['auth', 'verified'])->name('admin.dashboard');

Route::prefix('admin')->middleware('auth')->group(function () {

    //all user
    Route::get('users', [UsersController::class, 'index'])->name('admin.users.index');
    Route::post('users/update', [UsersController::class, 'update'])->name('admin.users.update');
    Route::get('/users/{id}', [UsersController::class, 'show'])->name('admin.users.show');

    // Plans
    Route::resource('all-plan', PlansController::class)->names([
        'index' => 'admin.plans.index',
        'create' => 'admin.plans.create',
        'store' => 'admin.plans.store',
        'show' => 'admin.plans.show',
        'edit' => 'admin.plans.edit',
        'update' => 'admin.plans.update',
        'destroy' => 'admin.plans.destroy'
    ]);

    // withdraw
    Route::resource('withdraw', WithdrawController::class)->names([
        'index' => 'admin.withdraw.index',
        'create' => 'admin.withdraw.create',
        'store' => 'admin.withdraw.store',
        'show' => 'admin.withdraw.show',
        'edit' => 'admin.withdraw.edit',
        'update' => 'admin.withdraw.update',
        'destroy' => 'admin.withdraw.destroy'
    ]);

    Route::resource('transactions', TransactionsController::class)->names([
        'index' => 'admin.transactions.index',
        'create' => 'admin.transactions.create',
        'store' => 'admin.transactions.store',
        'show' => 'admin.transactions.show',
        'edit' => 'admin.transactions.edit',
        'update' => 'admin.transactions.update',
        'destroy' => 'admin.transactions.destroy'
    ]);
    Route::resource('kyc', KycController::class)->names([
        'index' => 'admin.kyc.index',
        'create' => 'admin.kyc.create',
        'store' => 'admin.kyc.store',
        'show' => 'admin.kyc.show',
        'edit' => 'admin.kyc.edit',
        'update' => 'admin.kyc.update',
        'destroy' => 'admin.kyc.destroy'
    ]);
    Route::resource('clubs', AdminClubController::class)->names([
        'index' => 'admin.clubs.index',
        'create' => 'admin.clubs.create',
        'store' => 'admin.clubs.store',
        'show' => 'admin.clubs.show',
        'edit' => 'admin.clubs.edit',
        'update' => 'admin.clubs.update',
        'destroy' => 'admin.clubs.destroy'
    ]);
    Route::get('cron', [CronController::class, 'view'])->name('cron');


    Route::get('withdraws/settings', [WithdrawSettingsController::class, 'index'])->name('admin.withdraw.settings');
    Route::post('withdraws/settings', [WithdrawSettingsController::class, 'update'])->name('admin.withdraw.settings.update');


    Route::get('ReferralsSettings',[ReferralsSettingsController::class,'index'])->name('ReferralsSettings');
    Route::post('ReferralsSettings',[ReferralsSettingsController::class,'update'])->name('admin.referral.settings.update');


    //deposit
    Route::resource('deposit', DepositController::class)->names([
        'index' => 'admin.deposit.index',
        'create' => 'admin.deposit.create',
        'store' => 'admin.deposit.store',
        'show' => 'admin.deposit.show',
        'edit' => 'admin.deposit.edit',
        'update' => 'admin.deposit.update',
        'destroy' => 'admin.deposit.destroy'
    ]);

    // Category
    Route::resource('categories', CategoryController::class)->names([
        'index' => 'admin.categories.index',
        'create' => 'admin.categories.create',
        'store' => 'admin.categories.store',
        'show' => 'admin.categories.show',
        'edit' => 'admin.categories.edit',
        'update' => 'admin.categories.update',
        'destroy' => 'admin.categories.destroy'
    ]);


    // support ticket
    Route::get('/tickets', [AdminTicketController::class, 'index'])->name('admin.tickets.index');
    Route::get('/tickets/{id}', [AdminTicketController::class, 'show'])->name('admin.tickets.show');
    Route::post('tickets/{id}/reply', [AdminTicketController::class, 'reply'])->name('admin.tickets.reply');
    Route::post('tickets/{id}/close', [AdminTicketController::class, 'close'])->name('admin.tickets.close');


    // General Settings
    Route::get('general-settings', [GeneralSettingsController::class, 'index'])->name('admin.general.settings');
    Route::post('general-settings', [GeneralSettingsController::class, 'update'])->name('admin.general.settings.update');

});

Route::get('check',function(){
    return \Carbon\Carbon::now();
});

require __DIR__.'/auth.php';
require __DIR__.'/auths.php';
require __DIR__.'/user.php';
