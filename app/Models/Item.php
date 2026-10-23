<?php
// app/Models/Item.php

namespace App\Models;

use App\Enums\ItemConditionEnum;
use App\Enums\ItemStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'name', 'category_id', 'location_id', 'supplier_id', 'unit_id',
        'brand', 'type', 'serial_number', 'purchase_year', 'price',
        'condition', 'status', 'quantity','tracking_mode', 'item_type', 'min_stock', 'image', 'barcode',
        'warranty_start_date', 'warranty_duration_months','created_by', 'funding_source_id', 'updated_by'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'purchase_year' => 'integer',
        'condition' => ItemConditionEnum::class,
        'status' => ItemStatusEnum::class,
        'warranty_start_date' => 'date',
        'warranty_duration_months' => 'integer',
    ];

    // Relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }

    public function histories()
    {
        return $this->hasMany(ItemHistory::class);
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', ItemStatusEnum::TERSEDIA)
                     ->where('condition', '!=', ItemConditionEnum::RUSAK_BERAT);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'ILIKE', "%{$term}%")
              ->orWhere('code', 'ILIKE', "%{$term}%")
              ->orWhere('brand', 'ILIKE', "%{$term}%")
              ->orWhere('serial_number', 'ILIKE', "%{$term}%");
        });
    }

    // Helper
    public function isAvailable(): bool
    {
        return $this->status === ItemStatusEnum::TERSEDIA 
            && $this->condition !== ItemConditionEnum::RUSAK_BERAT
            && $this->quantity > 0;
    }

    public function fundingSource()
    {
        return $this->belongsTo(FundingSource::class);
    }

    /**
     * Resolve tracking mode efektif:
     * item.tracking_mode (override) 
     *   ?? category.default_tracking_mode 
     *   ?? 'per_unit'
     */
    public function getEffectiveTrackingModeAttribute(): string
    {
        return $this->tracking_mode
            ?? $this->category?->default_tracking_mode
            ?? 'per_unit';
    }

    /**
     * Cek apakah item ini per_unit
     */
    public function isPerUnit(): bool
    {
        return $this->effective_tracking_mode === 'per_unit';
    }

    /**
     * Cek apakah item ini per_batch
     */
    public function isPerBatch(): bool
    {
        return $this->effective_tracking_mode === 'per_batch';
    }

    /**
     * Label untuk tracking mode (untuk tampilan)
     */
    public function getTrackingModeLabelAttribute(): string
    {
        return match ($this->effective_tracking_mode) {
            'per_unit'  => 'Per Unit',
            'per_batch' => 'Per Batch',
            default     => 'Per Unit',
        };
    }

    /* ─────────── Item Type ─────────── */

    /**
     * Cek apakah item ini asset
     */
    public function isAsset(): bool
    {
        return $this->item_type === 'asset';
    }

    /**
     * Cek apakah item ini consumable (BHP)
     */
    public function isConsumable(): bool
    {
        return $this->item_type === 'consumable';
    }

    /**
     * Label item type
     */
    public function getItemTypeLabelAttribute(): string
    {
        return match ($this->item_type) {
            'asset'      => 'Aset',
            'consumable' => 'Habis Pakai',
            default      => 'Aset',
        };
    }

    /**
     * Cek apakah stok menipis
     */
    public function isLowStock(): bool
    {
        if (!$this->isConsumable()) return false;
        if ($this->min_stock === null) return false;
        
        return $this->quantity <= $this->min_stock;
    }

    /**
     * Badge untuk stok
     */
    public function getStockBadgeAttribute(): string
    {
        if ($this->isLowStock()) {
            return 'badge-critical';
        }
        
        return 'badge-success';
    }

    public function scopeConsumable($query)
    {
        return $query->where('item_type', 'consumable');
    }

    public function scopeAsset($query)
    {
        return $query->where('item_type', 'asset');
    }

    public function scopeLowStock($query)
    {
        return $query->where('item_type', 'consumable')
            ->whereNotNull('min_stock')
            ->whereColumn('quantity', '<=', 'min_stock');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class)->latest('created_at');
    }

    /**
     * Hitung otomatis tanggal berakhir garansi.
     */
    public function getWarrantyEndDateAttribute()
    {
        if (!$this->warranty_start_date || !$this->warranty_duration_months) {
            return null;
        }
        return $this->warranty_start_date->addMonths($this->warranty_duration_months);
    }

    /**
     * Cek apakah garansi masih aktif.
     */
    public function isWarrantyActive(): bool
    {
        if (!$this->warranty_end_date) {
            return false;
        }
        return $this->warranty_end_date->isFuture();
    }

    /**
     * Label status garansi untuk badge.
     */
    public function getWarrantyStatusAttribute(): string
    {
        if (!$this->warranty_start_date) {
            return 'Tidak Ada Data';
        }
        if ($this->isWarrantyActive()) {
            return 'Aktif';
        }
        return 'Habis';
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function receivingItems()
    {
        return $this->hasMany(ReceivingItem::class);
    }
}