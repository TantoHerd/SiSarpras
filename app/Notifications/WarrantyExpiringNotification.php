<?php
// app/Notifications/WarrantyExpiringNotification.php

namespace App\Notifications;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class WarrantyExpiringNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Item $item
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $endDate = $this->item->warranty_end_date->format('d M Y');
        $daysLeft = now()->diffInDays($this->item->warranty_end_date, false);

        return [
            'type'       => 'warranty_expiring',
            'item_id'    => $this->item->id,
            'item_name'  => $this->item->name,
            'item_code'  => $this->item->code,
            'end_date'   => $endDate,
            'days_left'  => $daysLeft,
            'message'    => "Garansi \"{$this->item->name}\" akan berakhir dalam {$daysLeft} hari ({$endDate}).",
            'url'        => route('items.show', $this->item->id),
        ];
    }
}