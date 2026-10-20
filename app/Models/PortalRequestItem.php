<?php
// app/Models/PortalRequestItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortalRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'portal_request_id',
        'item_id',
        'quantity',
        'note',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function portalRequest(): BelongsTo
    {
        return $this->belongsTo(PortalRequest::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}