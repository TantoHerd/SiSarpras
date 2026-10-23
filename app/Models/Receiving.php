<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Receiving extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'receiving_number', 'supplier_id', 'funding_source_id',
        'receiving_date', 'invoice_number', 'note', 'created_by',
    ];

    protected $casts = [
        'receiving_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function fundingSource()
    {
        return $this->belongsTo(FundingSource::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(ReceivingItem::class);
    }

    /**
     * Generate nomor penerimaan: RCV-YYYY-MM-NNN
     */
    public static function generateNumber(): string
    {
        $prefix = 'RCV-' . now()->format('Y-m');
        
        $last = self::withTrashed()
            ->where('receiving_number', 'LIKE', "{$prefix}-%")
            ->orderByDesc('receiving_number')
            ->first();

        if ($last) {
            $lastNumber = (int) substr($last->receiving_number, -3);
            $next = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $next = '001';
        }

        return "{$prefix}-{$next}";
    }
}