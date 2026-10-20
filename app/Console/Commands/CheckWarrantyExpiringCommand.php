<?php
// app/Console/Commands/CheckWarrantyExpiringCommand.php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\User;
use App\Notifications\WarrantyExpiringNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckWarrantyExpiringCommand extends Command
{
    protected $signature = 'warranty:check-expiring {--force : Kirim notifikasi meskipun sudah ada}';
    protected $description = 'Cek aset dengan garansi yang akan habis dan kirim notifikasi';

    public function handle(): int
    {
        $this->info('Checking warranty expiring items...');

        // Ambil setting berapa hari sebelum expired
        $reminderDays = (int) DB::table('settings')->where('key', 'warranty_reminder_days')->value('value') ?? 30;

        $targetDate = now()->addDays($reminderDays)->startOfDay();

        // Cari aset dengan garansi yang akan habis (dalam rentang hari ini s/d targetDate)
        $items = Item::where('item_type', 'asset')
            ->whereNotNull('warranty_start_date')
            ->whereNotNull('warranty_duration_months')
            ->get()
            ->filter(function ($item) use ($targetDate) {
                $endDate = $item->warranty_end_date;
                return $endDate && $endDate->between(now()->startOfDay(), $targetDate);
            });

        if ($items->isEmpty()) {
            $this->info("✅ Tidak ada aset dengan garansi yang akan habis dalam {$reminderDays} hari.");
            return self::SUCCESS;
        }

        $this->info("📦 Ditemukan {$items->count()} aset dengan garansi yang akan habis.");

        $recipients = User::whereHas('role', function ($q) {
            $q->whereIn('name', ['admin', 'petugas_sarpras']);
        })->where('is_active', true)->get();

        if ($recipients->isEmpty()) {
            $this->warn('⚠️ Tidak ada admin/petugas aktif untuk menerima notifikasi.');
            return self::SUCCESS;
        }

        $sentCount = 0;
        $skippedCount = 0;

        foreach ($items as $item) {
            foreach ($recipients as $user) {
                if (!$this->option('force')) {
                    // Cek duplikat notifikasi dalam 7 hari terakhir
                    $recent = $user->notifications()
                        ->where('type', WarrantyExpiringNotification::class)
                        ->where('created_at', '>=', now()->subDays(7))
                        ->get()
                        ->contains(fn($notif) => isset($notif->data['item_id']) && $notif->data['item_id'] === $item->id);

                    if ($recent) {
                        $skippedCount++;
                        continue;
                    }
                }

                $user->notify(new WarrantyExpiringNotification($item));
                $sentCount++;
            }
        }

        $this->info("✅ Selesai! Terkirim: {$sentCount}, Dilewati: {$skippedCount}");

        return self::SUCCESS;
    }
}