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
        // When a goods inward with multiple items is created, increase the stock for each line
        if ($goodsInward->relationLoaded('items')) {
            $lines = $goodsInward->items;
        } else {
            $lines = $goodsInward->items()->get();
        }

        foreach ($lines as $line) {
            // guard: ensure we have an item id and jumlah
            $itemId = $line->item_id ?? null;
            $qty = isset($line->jumlah) ? (int) $line->jumlah : null;
            if ($itemId && $qty > 0) {
                $this->stockService->increase($itemId, $qty, 'goods_inwards', $goodsInward->id, 'Penerimaan barang (observer)');
            }
        }
    }

    public function updated(GoodsInward $goodsInward): void
    {
        // For multi-line goods inwards we don't attempt to diff here.
        // If business logic requires adjusting stock on edits, implement a diff between
        // original lines and current lines. For now, no-op to avoid double-applying stock changes.
        return;
    }

    public function deleted(GoodsInward $goodsInward): void
    {
        // If a goods_inward record is deleted, rollback the stock increase for each line
        $lines = $goodsInward->items()->get();
        foreach ($lines as $line) {
            $itemId = $line->item_id ?? null;
            $qty = isset($line->jumlah) ? (int) $line->jumlah : null;
            if ($itemId && $qty > 0) {
                $this->stockService->decrease($itemId, $qty, 'goods_inwards', $goodsInward->id, 'Rollback penerimaan (observer)');
            }
        }
    }
}
