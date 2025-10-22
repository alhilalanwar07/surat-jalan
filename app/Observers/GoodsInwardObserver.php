<?php

namespace App\Observers;

use App\Models\GoodsInward;
use App\Services\StockService;

class GoodsInwardObserver
{
    protected StockService $stockService;

    public function __construct()
    {
        $this->stockService = new StockService();
    }

    public function created(GoodsInward $goodsInward): void
    {
        // When goods are received, increase the stock
        $this->stockService->increase($goodsInward->item_id, $goodsInward->jumlah);
    }

    public function updated(GoodsInward $goodsInward): void
    {
        // If jumlah changed, apply the delta to stock
        $original = $goodsInward->getOriginal('jumlah') ?? 0;
        $current = $goodsInward->jumlah;
        $delta = $current - $original;
        if ($delta > 0) {
            $this->stockService->increase($goodsInward->item_id, $delta);
        } elseif ($delta < 0) {
            $this->stockService->decrease($goodsInward->item_id, abs($delta));
        }
    }

    public function deleted(GoodsInward $goodsInward): void
    {
        // If a goods_inward record is deleted, rollback the stock increase
        // (depending on business logic you may instead prevent delete or soft delete)
        $this->stockService->decrease($goodsInward->item_id, $goodsInward->jumlah);
    }
}
