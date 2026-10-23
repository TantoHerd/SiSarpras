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
use App\Http\Controllers\FundingSourceController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\PortalRequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\StockRequestController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ReceivingController;

/*
|----------------------------------------------------------------------
| ROOT REDIRECT
|----------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|----------------------------------------------------------------------
| PORTAL SISWA (Public, no auth)
| Rate limited: 3 request per menit per IP
|----------------------------------------------------------------------
*/
Route::prefix('portal')
    ->name('portal.')
    ->middleware('throttle:60,1')
    ->group(function () {
        Route::get('/', [PortalController::class, 'index'])->name('index');
        
        // ← BARU: Halaman BHP & Peminjaman
        Route::get('/bhp', [PortalController::class, 'bhpForm'])->name('bhp');
        Route::post('/bhp', [PortalController::class, 'bhpStore'])->name('bhp.store');
        
        Route::get('/loan', [PortalController::class, 'loanForm'])->name('loan');
        Route::post('/loan', [PortalController::class, 'loanStore'])->name('loan.store');
        
        // API Autofill
        Route::get('/search-identity', [PortalController::class, 'searchIdentity'])->name('search-identity');
        
        Route::get('/success', [PortalController::class, 'success'])->name('success');
        Route::get('/status', [PortalController::class, 'status'])->name('status');
    });

/*
|----------------------------------------------------------------------
| AUTH ROUTES (butuh login + verified)
|----------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ← BARU: Notifications
    Route::prefix('notifications')
        ->name('notifications.')
        ->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/dropdown', [NotificationController::class, 'dropdown'])->name('dropdown');
            Route::get('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
            Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
            Route::delete('/', [NotificationController::class, 'destroyAll'])->name('destroy-all');
        });

    /*
    |----------------------------------------------------------------------
    | ADMIN ONLY
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        // Users
        Route::patch('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('/users/import', [UserController::class, 'importForm'])->name('users.import.form');
        Route::post('/users/import', [UserController::class, 'importStore'])->name('users.import.store');
        Route::get('/users/import/template', [UserController::class, 'downloadTemplate'])->name('users.import.template');
        Route::resource('users', UserController::class);

        // Master Data
        Route::resource('categories', CategoryController::class);
        Route::resource('locations', LocationController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('units', UnitController::class)->except(['show']);

        // Funding Sources (CRUD - admin only)
        Route::get('/funding-sources/create', [FundingSourceController::class, 'create'])->name('funding-sources.create');
        Route::post('/funding-sources', [FundingSourceController::class, 'store'])->name('funding-sources.store');
        Route::get('/funding-sources/export/excel', [FundingSourceController::class, 'exportExcel'])->name('funding-sources.export.excel');
        Route::get('/funding-sources/{funding_source}/edit', [FundingSourceController::class, 'edit'])->name('funding-sources.edit');
        Route::put('/funding-sources/{funding_source}', [FundingSourceController::class, 'update'])->name('funding-sources.update');
        Route::delete('/funding-sources/{funding_source}', [FundingSourceController::class, 'destroy'])->name('funding-sources.destroy');

        // Students — import (harus sebelum resource)
        Route::get('/students/import', [StudentController::class, 'importForm'])->name('students.import.form');
        Route::post('/students/import', [StudentController::class, 'importStore'])->name('students.import.store');
        Route::get('/students/import/template', [StudentController::class, 'downloadTemplate'])->name('students.import.template');

        // Students (data siswa untuk portal)
        Route::resource('students', StudentController::class);

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
    | ADMIN + PETUGAS + KEPALA SEKOLAH (Laporan + Funding Index)
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

        // Funding Sources — index only (read-only untuk petugas & kepsek)
        Route::get('/funding-sources', [FundingSourceController::class, 'index'])->name('funding-sources.index');
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
        Route::get('/items/import', [ItemController::class, 'importForm'])->name('items.import.form');
        Route::post('/items/import', [ItemController::class, 'importStore'])->name('items.import.store');
        Route::get('/items/import/template', [ItemController::class, 'downloadTemplate'])->name('items.import.template');
        Route::resource('items', ItemController::class);

        // Receiving
        Route::resource('receivings', ReceivingController::class)->only(['index', 'create', 'store', 'show']);

        // Loans — return dulu (statis dengan parameter), baru resource
        Route::get('/loans/{loan}/return', [LoanController::class, 'returnForm'])->name('loans.return.form');
        Route::post('/loans/{loan}/return', [LoanController::class, 'returnStore'])->name('loans.return.store');
        
        // Resource loans — kecuali show & create (sudah di grup atas)
        Route::resource('loans', LoanController::class)->except(['show', 'create']);

        // Maintenances
        Route::patch('/maintenances/{maintenance}/complete', [MaintenanceController::class, 'complete'])->name('maintenances.complete');
        Route::resource('maintenances', MaintenanceController::class);

        // Portal Requests (prefix admin/portal-requests — biar tidak conflict dengan /portal)
        Route::prefix('admin/portal-requests')
            ->name('portal-requests.')
            ->group(function () {
                Route::get('/', [PortalRequestController::class, 'index'])->name('index');
                Route::get('/{portalRequest}', [PortalRequestController::class, 'show'])->name('show');
                Route::post('/{portalRequest}/approve', [PortalRequestController::class, 'approve'])->name('approve');
                Route::post('/{portalRequest}/reject', [PortalRequestController::class, 'reject'])->name('reject');
            });

        // ← BARU: Stock Opname
        Route::prefix('stock-opnames')
            ->name('stock-opnames.')
            ->group(function () {
                Route::get('/', [StockOpnameController::class, 'index'])->name('index');
                Route::get('/create', [StockOpnameController::class, 'create'])->name('create');
                Route::post('/', [StockOpnameController::class, 'store'])->name('store');
                Route::get('/{stockOpname}', [StockOpnameController::class, 'show'])->name('show');
                Route::put('/{stockOpname}/physical', [StockOpnameController::class, 'updatePhysical'])->name('update-physical');
                Route::post('/{stockOpname}/complete', [StockOpnameController::class, 'complete'])->name('complete');
                Route::post('/{stockOpname}/cancel', [StockOpnameController::class, 'cancel'])->name('cancel');
                Route::delete('/{stockOpname}', [StockOpnameController::class, 'destroy'])->name('destroy');
            });
    });

    /*
    |----------------------------------------------------------------------
    | STOCK MOVEMENTS (Admin + Petugas)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin,petugas_sarpras')
        ->prefix('stock-movements')
        ->name('stock-movements.')
        ->group(function () {
            Route::get('/', [StockMovementController::class, 'index'])->name('index');
            Route::get('/{stockMovement}', [StockMovementController::class, 'show'])->name('show');
        });

    /*
    |----------------------------------------------------------------------
    | STOCK REQUESTS (Guru + Staf + Petugas + Admin)
    |----------------------------------------------------------------------
    */
    Route::prefix('stock-requests')
        ->name('stock-requests.')
        ->group(function () {
            // Index — guru lihat milik sendiri, petugas/admin lihat semua
            Route::get('/', [StockRequestController::class, 'index'])->name('index');

            // Create (guru + staf + petugas + admin)
            Route::middleware('role:admin,petugas_sarpras,guru')
                ->group(function () {
                    Route::get('/create', [StockRequestController::class, 'create'])->name('create');
                    Route::post('/', [StockRequestController::class, 'store'])->name('store');
                });

            // Approve/Reject (petugas + admin)
            Route::middleware('role:admin,petugas_sarpras')
                ->group(function () {
                    Route::post('/{stockRequest}/approve', [StockRequestController::class, 'approve'])->name('approve');
                    Route::post('/{stockRequest}/reject', [StockRequestController::class, 'reject'])->name('reject');
                });

            // Show & Delete (semua role dengan ownership check di controller)
            Route::get('/{stockRequest}', [StockRequestController::class, 'show'])->name('show');
            Route::delete('/{stockRequest}', [StockRequestController::class, 'destroy'])->name('destroy');
        });
});

require __DIR__.'/auth.php';