<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\user\AuthController;
use App\Http\Controllers\user\DashboardController;

Route::prefix('user')->middleware('auth')->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('user.index');
    Route::post('email/verification-notification',[EmailController::class,'sendVerificationEmail']);
    Route::get('verify-email/{id}/{hash}',[EmailController::class,'verify'])->middleware(['signed'])->name('verification.verify');

    Route::post('logout', [AuthController::class, 'logout'])->name('user.logout');


});
