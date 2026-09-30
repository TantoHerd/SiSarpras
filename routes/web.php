<?php
// routes/web.php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Redirect Root
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |----------------------------------------------------------------------
    | DASHBOARD (semua role)
    |----------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | PROFIL (semua role)
    |----------------------------------------------------------------------
    */
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
        
        // ============ USERS ============
        Route::patch('/users/{user}/toggle-active', [\App\Http\Controllers\UserController::class, 'toggleActive'])
            ->name('users.toggle-active');
        Route::post('/users/{user}/reset-password', [\App\Http\Controllers\UserController::class, 'resetPassword'])
            ->name('users.reset-password');
        Route::resource('users', \App\Http\Controllers\UserController::class);

        // ============ CATEGORIES ============
        Route::resource('categories', \App\Http\Controllers\CategoryController::class);

        // ============ LOCATIONS ============
        Route::resource('locations', \App\Http\Controllers\LocationController::class);

        // ============ SUPPLIERS ============
        Route::resource('suppliers', \App\Http\Controllers\SupplierController::class);

        // ============ PLACEHOLDER (belum dibuat) ============
        Route::get('/settings', fn() => 'Halaman Pengaturan Sistem (Admin Only)')->name('settings.index');
    });

    /*
    |----------------------------------------------------------------------
    | ADMIN + PETUGAS + GURU
    | Grup ini HARUS di atas resource items & loans untuk menghindari
    | route statis seperti /loans/request tertutup oleh /loans/{loan}
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin,petugas_sarpras,guru')->group(function () {
        // Items tersedia
        Route::get('/items/available', fn() => 'Halaman Barang Tersedia')->name('items.available');

        // Loans dari sisi Guru
        Route::get('/my-loans', [LoanController::class, 'myLoans'])->name('loans.my');
        Route::get('/loans/request', [LoanController::class, 'requestForm'])->name('loans.request');
        Route::post('/loans/request', [LoanController::class, 'requestStore'])->name('loans.request.store');

        // Detail Loan — boleh diakses semua role, tapi ada cek ownership di controller
        Route::get('/loans/{loan}', [LoanController::class, 'show'])->name('loans.show');
    });

    /*
    |----------------------------------------------------------------------
    | ADMIN + PETUGAS + KEPALA SEKOLAH (Laporan)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin,petugas_sarpras,kepala_sekolah')->group(function () {
        Route::get('/reports', fn() => 'Halaman Laporan')->name('reports.index');
        Route::get('/reports/pdf', fn() => 'Export Laporan PDF')->name('reports.pdf');
        Route::get('/reports/excel', fn() => 'Export Laporan Excel')->name('reports.excel');
    });

    /*
    |----------------------------------------------------------------------
    | ADMIN + PETUGAS SARPRAS (CRUD Items & Loans)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin,petugas_sarpras')->group(function () {

        // ============ ITEMS ============
        // Barcode harus di atas resource items
        Route::get('/items/{item}/barcode', [ItemController::class, 'barcode'])->name('items.barcode');
        Route::resource('items', ItemController::class);

        // ============ LOANS ============
        // Return — harus di atas resource loans
        Route::get('/loans/{loan}/return', [LoanController::class, 'returnForm'])->name('loans.return.form');
        Route::post('/loans/{loan}/return', [LoanController::class, 'returnStore'])->name('loans.return.store');

        // Resource loans — kecuali show (sudah dipindah ke grup di atas)
        Route::resource('loans', LoanController::class)->except(['show']);

        // ============ MAINTENANCE ============
        Route::resource('maintenances', \App\Http\Controllers\MaintenanceController::class);
    });
});

/*
|--------------------------------------------------------------------------
| Auth Routes (Login, Logout, Password Reset)
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';