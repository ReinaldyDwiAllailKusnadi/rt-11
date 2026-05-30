<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LetterController;
use App\Http\Controllers\ReceiptController;

// Public routes
Route::get('/', [PublicController::class, 'dashboard'])->name('public.dashboard');
Route::get('/receipt/{resident}', [ReceiptController::class, 'show'])->name('receipt.show');

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes (Protected by Auth)
Route::middleware('auth')->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/admin/change-password', [AuthController::class, 'updatePassword'])->name('admin.change-password');
    
    // Residents CRUD
    Route::resource('/admin/residents', ResidentController::class)->except(['create', 'show', 'edit']);
    Route::post('/admin/residents/reset', [ResidentController::class, 'reset'])->name('residents.reset');
    
    // Payments / Transactions
    Route::get('/admin/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/admin/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/admin/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/admin/payments/{payment}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::put('/admin/payments/{payment}', [PaymentController::class, 'update'])->name('payments.update');
    Route::delete('/admin/payments/{payment}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    Route::get('/admin/payments/status', [PaymentController::class, 'statusBayar'])->name('payments.status');
    Route::get('/admin/payments/riwayat', [PaymentController::class, 'riwayatWarga'])->name('payments.riwayat');
    
    // Initial Balance (Saldo Awal)
    Route::get('/admin/settings/saldo-awal', [SettingController::class, 'showSaldoAwal'])->name('settings.saldo-awal');
    Route::post('/admin/settings/saldo-awal', [SettingController::class, 'storeSaldoAwal'])->name('settings.saldo-awal.store');
    
    // Excel Reports
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/admin/reports/export', [ReportController::class, 'exportExcel'])->name('reports.export');
    // Letter Generation
    Route::get('/admin/letters', [LetterController::class, 'index'])->name('letters.index');
    Route::post('/admin/letters/generate', [LetterController::class, 'generate'])->name('letters.generate');
    Route::post('/admin/letters/export-word', [LetterController::class, 'exportWord'])->name('letters.export-word');
});
