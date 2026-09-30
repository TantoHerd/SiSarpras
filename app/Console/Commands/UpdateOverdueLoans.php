<?php
// app/Console/Commands/UpdateOverdueLoans.php

namespace App\Console\Commands;

use App\Services\LoanService;
use Illuminate\Console\Command;

class UpdateOverdueLoans extends Command
{
    /**
     * Nama command yang dipanggil via artisan
     */
    protected $signature = 'loans:update-overdue';

    /**
     * Deskripsi command
     */
    protected $description = 'Update status peminjaman yang sudah melewati jatuh tempo menjadi "terlambat"';

    public function handle(LoanService $loanService): int
    {
        $this->info('Mulai memeriksa peminjaman yang terlambat...');

        $count = $loanService->updateOverdueLoans();

        if ($count > 0) {
            $this->info("✅ Berhasil update {$count} peminjaman menjadi terlambat.");
        } else {
            $this->info('✅ Tidak ada peminjaman yang terlambat.');
        }

        return Command::SUCCESS;
    }
}