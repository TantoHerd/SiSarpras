<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(\App\Repositories\ItemRepository::class);
        $this->app->bind(\App\Repositories\LoanRepository::class);
        $this->app->bind(\App\Repositories\UserRepository::class);
        $this->app->bind(\App\Repositories\CategoryRepository::class);
        $this->app->bind(\App\Repositories\LocationRepository::class);  
        $this->app->bind(\App\Repositories\SupplierRepository::class);   
        $this->app->bind(\App\Repositories\MaintenanceRepository::class);
        $this->app->bind(\App\Repositories\ReportRepository::class);                                                       

        $this->app->bind(
            \App\Repositories\LoanRepository::class,
            \App\Repositories\LoanRepository::class
        );

        // Service bindings
        $this->app->singleton(\App\Services\ItemService::class);
        $this->app->singleton(\App\Services\SettingService::class);
        $this->app->singleton(\App\Services\LoanService::class);
        $this->app->singleton(\App\Services\UserService::class);
        $this->app->singleton(\App\Services\CategoryService::class);
        $this->app->singleton(\App\Services\LocationService::class);
        $this->app->singleton(\App\Services\SupplierService::class);
        $this->app->singleton(\App\Services\MaintenanceService::class);
        $this->app->singleton(\App\Services\Reports\ReportService::class);
    }

    public function boot(): void
    {
        /**
         * Blade directive @role('admin')
         * Mendukung multiple role: @role('admin,petugas_sarpras')
         */
        Paginator::useTailwind();
        Blade::if('role', function (string $roles) {
            if (! Auth::check()) {
                return false;
            }

            $userRole = Auth::user()->role->name ?? null;
            $allowedRoles = array_map('trim', explode(',', $roles));

            return $userRole && in_array($userRole, $allowedRoles);
        });

        /**
         * Blade directive @notrole('guru')
         */
        Blade::if('notrole', function (string $roles) {
            if (! Auth::check()) {
                return false;
            }

            $userRole = Auth::user()->role->name ?? null;
            $allowedRoles = array_map('trim', explode(',', $roles));

            return $userRole && ! in_array($userRole, $allowedRoles);
        });
    }
}