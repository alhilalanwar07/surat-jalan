<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsInwardItem extends Model
{
    protected $fillable = ['goods_inward_id', 'item_id', 'jumlah', 'harga', 'note'];

    protected $casts = [
        'jumlah' => 'integer',
        'harga' => 'decimal:2',
    ];

    public function goodsInward(): BelongsTo
    {
        return $this->belongsTo(GoodsInward::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
