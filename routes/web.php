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
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::resource('users', UserController::class);

        Route::resource('categories', CategoryController::class);
        Route::resource('locations', LocationController::class);
        Route::resource('suppliers', SupplierController::class);

        Route::get('/settings', fn() => 'Halaman Pengaturan Sistem')->name('settings.index');
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
        Route::get('/reports', fn() => 'Halaman Laporan')->name('reports.index');
        Route::get('/reports/pdf', fn() => 'Export PDF')->name('reports.pdf');
        Route::get('/reports/excel', fn() => 'Export Excel')->name('reports.excel');
    });

    /*
    |----------------------------------------------------------------------
    | ADMIN + PETUGAS SARPRAS
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin,petugas_sarpras')->group(function () {
        // Items
        Route::get('/items/{item}/barcode', [ItemController::class, 'barcode'])->name('items.barcode');
        Route::resource('items', ItemController::class);

        // Loans — return dulu (statis dengan parameter), baru resource
        Route::get('/loans/{loan}/return', [LoanController::class, 'returnForm'])->name('loans.return.form');
        Route::post('/loans/{loan}/return', [LoanController::class, 'returnStore'])->name('loans.return.store');
        
        // Resource loans — kecuali show & create (sudah di grup atas)
        Route::resource('loans', LoanController::class)->except(['show', 'create']);

        // Maintenances
        Route::patch('/maintenances/{maintenance}/complete', [MaintenanceController::class, 'complete'])
            ->name('maintenances.complete');
        Route::resource('maintenances', MaintenanceController::class);
    });
});

require __DIR__.'/auth.php';