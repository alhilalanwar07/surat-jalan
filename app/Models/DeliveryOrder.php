<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Purpose;

class DeliveryOrder extends Model
{
    use SoftDeletes;

    protected $fillable = ['nomor_sj', 'tanggal', 'purpose_id', 'nomor_po', 'nama_sopir', 'nomor_kendaraan', 'purpose', 'alokasi', 'dokumentasi'];


    // ensure confirmed_at is cast to datetime so ->format() is available in views
    protected $casts = [
        'tanggal' => 'date',
        'confirmed_at' => 'datetime',
    ];

    public function purpose(): BelongsTo
    {
        return $this->belongsTo(Purpose::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(DeliveryOrderItem::class);
    }
}
