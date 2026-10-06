<?php
// routes/web.php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\FundingSourceController;  // ← BARU
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |----------------------------------------------------------------------
    | ADMIN ONLY
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        // Users
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::resource('users', UserController::class);

        // Master Data
        Route::resource('categories', CategoryController::class);
        Route::resource('locations', LocationController::class);
        Route::resource('suppliers', SupplierController::class);

        // ▼▼▼ BARU: Funding Sources (CRUD - admin only) ▼▼▼
        Route::get('/funding-sources/create', [FundingSourceController::class, 'create'])->name('funding-sources.create');
        Route::post('/funding-sources', [FundingSourceController::class, 'store'])->name('funding-sources.store');
        Route::get('/funding-sources/export/excel', [FundingSourceController::class, 'exportExcel'])->name('funding-sources.export.excel');
        Route::get('/funding-sources/{funding_source}/edit', [FundingSourceController::class, 'edit'])->name('funding-sources.edit');
        Route::put('/funding-sources/{funding_source}', [FundingSourceController::class, 'update'])->name('funding-sources.update');
        Route::delete('/funding-sources/{funding_source}', [FundingSourceController::class, 'destroy'])->name('funding-sources.destroy');
        // ▲▲▲ END BARU ▲▲▲

        // Settings
        Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings/update', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
        Route::delete('/settings/logo', [\App\Http\Controllers\SettingController::class, 'removeLogo'])->name('settings.remove-logo');
        Route::post('/settings/reset', [\App\Http\Controllers\SettingController::class, 'reset'])->name('settings.reset');
    });

    /*
    |----------------------------------------------------------------------
    | ADMIN + PETUGAS + GURU
    | PENTING: Route statis dulu, baru dinamis
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin,petugas_sarpras,guru')->group(function () {
        Route::get('/items/available', fn() => 'Halaman Barang Tersedia')->name('items.available');
        Route::get('/my-loans', [LoanController::class, 'myLoans'])->name('loans.my');
        
        // Loans request (Guru & semua)
        Route::get('/loans/request', [LoanController::class, 'requestForm'])->name('loans.request');
        Route::post('/loans/request', [LoanController::class, 'requestStore'])->name('loans.request.store');

        // Loans create (hanya Admin & Petugas) — harus sebelum {loan}
        Route::middleware('role:admin,petugas_sarpras')->group(function () {
            Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
        });

        // Loans show — PALING BAWAH (dinamis)
        Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
    });

    /*
    |----------------------------------------------------------------------
    | ADMIN + PETUGAS + KEPALA SEKOLAH (Laporan)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin,petugas_sarpras,kepala_sekolah')->group(function () {
        // Halaman Index
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

        // Inventaris
        Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
        Route::get('/reports/inventory/pdf', [ReportController::class, 'inventoryPdf'])->name('reports.inventory.pdf');
        Route::get('/reports/inventory/excel', [ReportController::class, 'inventoryExcel'])->name('reports.inventory.excel');
        
        // Peminjaman
        Route::get('/reports/loans', [ReportController::class, 'loans'])->name('reports.loans');
        Route::get('/reports/loans/pdf', [ReportController::class, 'loansPdf'])->name('reports.loans.pdf');
        Route::get('/reports/loans/excel', [ReportController::class, 'loansExcel'])->name('reports.loans.excel');

        // Perawatan
        Route::get('/reports/maintenances', [ReportController::class, 'maintenances'])->name('reports.maintenances');
        Route::get('/reports/maintenances/pdf', [ReportController::class, 'maintenancesPdf'])->name('reports.maintenances.pdf');
        Route::get('/reports/maintenances/excel', [ReportController::class, 'maintenancesExcel'])->name('reports.maintenances.excel');

        // ▼▼▼ BARU: Funding Sources (index - admin, petugas, kepsek) ▼▼▼
        Route::get('/funding-sources', [FundingSourceController::class, 'index'])->name('funding-sources.index');
        // ▲▲▲ END BARU ▲▲▲
    });

    /*
    |----------------------------------------------------------------------
    | ADMIN + PETUGAS SARPRAS
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin,petugas_sarpras')->group(function () {
        // Items
        Route::get('/items/barcode-batch', [ItemController::class, 'barcodeBatch'])->name('items.barcode-batch');
        Route::get('/items/{item}/barcode', [ItemController::class, 'barcode'])->name('items.barcode');
        Route::resource('items', ItemController::class);

        // Loans — return dulu (statis dengan parameter), baru resource
        Route::get('/loans/{loan}/return', [LoanController::class, 'returnForm'])->name('loans.return.form');
        Route::post('/loans/{loan}/return', [LoanController::class, 'returnStore'])->name('loans.return.store');
        
        // Resource loans — kecuali show & create (sudah di grup atas)
        Route::resource('loans', LoanController::class)->except(['show', 'create']);

        // Maintenances
        Route::patch('/maintenances/{maintenance}/complete', [MaintenanceController::class, 'complete'])->name('maintenances.complete');
        Route::resource('maintenances', MaintenanceController::class);
    });
});

require __DIR__.'/auth.php';