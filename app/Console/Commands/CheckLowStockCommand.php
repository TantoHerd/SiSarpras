<?php
// app/Console/Commands/CheckLowStockCommand.php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\User;
use App\Notifications\LowStockNotification;
use Illuminate\Console\Command;

class CheckLowStockCommand extends Command
{
    protected $signature = 'stock:check-low-stock
                            {--force : Kirim notifikasi meskipun sudah ada notif hari ini}';

    protected $description = 'Cek item dengan stok menipis dan kirim notifikasi';

    public function handle(): int
    {
        $this->info('Checking low stock items...');

        // Ambil item dengan stok menipis
        $lowStockItems = Item::lowStock()
            ->with(['category'])
            ->get();

        if ($lowStockItems->isEmpty()) {
            $this->info('✅ Tidak ada item dengan stok menipis.');
            return self::SUCCESS;
        }

        $this->info("📦 Ditemukan {$lowStockItems->count()} item dengan stok menipis.");

        // Ambil admin + petugas
        $recipients = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['admin', 'petugas_sarpras']);
        })->where('is_active', true)->get();

        if ($recipients->isEmpty()) {
            $this->warn('⚠️ Tidak ada admin/petugas aktif untuk menerima notifikasi.');
            return self::SUCCESS;
        }

        $this->info("👥 Mengirim notifikasi ke {$recipients->count()} user...");

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($lowStockItems as $item) {
            foreach ($recipients as $user) {
                // ← PERBAIKAN: Filter di PHP, hindari operator ->>
                if (!$this->option('force')) {
                    $recentNotifs = $user->notifications()
                        ->where('type', LowStockNotification::class)
                        ->where('created_at', '>=', now()->subHours(12))
                        ->get();

                    $recent = $recentNotifs->contains(function ($notif) use ($item) {
                        $data = $notif->data;
                        return isset($data['item_id']) && (int) $data['item_id'] === $item->id;
                    });

                    if ($recent) {
                        $skippedCount++;
                        continue;
                    }
                }

                $user->notify(new LowStockNotification($item));
                $sentCount++;
            }
        }

        $this->newLine();
        $this->info("✅ Selesai!");
        $this->table(
            ['Metrik', 'Jumlah'],
            [
                ['Item menipis', $lowStockItems->count()],
                ['Recipient', $recipients->count()],
                ['Notifikasi terkirim', $sentCount],
                ['Dilewati (sudah ada)', $skippedCount],
            ]
        );

        return self::SUCCESS;
    }
}