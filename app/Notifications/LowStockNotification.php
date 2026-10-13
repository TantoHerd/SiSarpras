<?php
// app/Notifications/LowStockNotification.php

namespace App\Notifications;

use App\Models\Item;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Item $item
    ) {}

    /**
     * Channel — hanya database (bisa ditambah mail kalau perlu)
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Data yang disimpan di DB
     */
    public function toArray(object $notifiable): array
    {
        return [
            'item_id'       => $this->item->id,
            'item_name'     => $this->item->name,
            'item_code'     => $this->item->code,
            'quantity'      => $this->item->quantity,
            'min_stock'     => $this->item->min_stock,
            'category_name' => $this->item->category->name ?? '-',
            'message'       => "Stok \"{$this->item->name}\" menipis: {$this->item->quantity} unit (min: {$this->item->min_stock})",
            'url'           => route('items.show', $this->item->id),
        ];
    }
}