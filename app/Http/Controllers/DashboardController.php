<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Repositories\ItemRepository;
use App\Repositories\LoanRepository;
use App\Repositories\MaintenanceRepository;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected ItemRepository $itemRepository,
        protected LoanRepository $loanRepository,
        protected MaintenanceRepository $maintenanceRepository
    ) {}

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Statistik items
        $stats = $this->itemRepository->getStats();

        // Statistik loans
        $loanStats = null;
        $activeLoans = null;
        $myLoans = null;

        if ($user->isAdmin() || $user->isPetugas() || $user->isKepalaSekolah()) {
            $loanStats = $this->loanRepository->getStats();
        }

        if ($user->isAdmin() || $user->isPetugas()) {
            $activeLoans = $this->loanRepository->getAllActive(5);
        }

        if ($user->isGuru()) {
            $myLoans = $this->loanRepository->getActiveByUser($user->id, 5);
        }

        // Maintenance (untuk admin & petugas)
        $maintenanceStats = null;
        $upcomingMaintenances = null;

        if ($user->isAdmin() || $user->isPetugas()) {
            $maintenanceStats = $this->maintenanceRepository->getStats();
            $upcomingMaintenances = $this->maintenanceRepository->getUpcoming(7, 5);
        }

        return view('dashboard', compact(
            'stats',
            'loanStats',
            'myLoans',
            'activeLoans',
            'maintenanceStats',
            'upcomingMaintenances'
        ));
    }
}