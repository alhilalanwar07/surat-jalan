<?php

namespace App\Observers;

use App\Models\DeliveryOrderItem;
use App\Services\StockService;

class DeliveryOrderItemObserver
{
    protected StockService $stockService;

    public function __construct()
    {
        $this->stockService = new StockService();
    }

    public function created(DeliveryOrderItem $item): void
    {
        // When item is added to delivery order, decrease stock
        $this->stockService->decrease($item->item_id, $item->jumlah);
    }

    public function updated(DeliveryOrderItem $item): void
    {
        // If jumlah changed, compute delta (note: decreasing DO increases stock and vice versa)
        $original = $item->getOriginal('jumlah') ?? 0;
        $current = $item->jumlah;
        $delta = $current - $original;
        if ($delta > 0) {
            // more items sent, decrease stock
            $this->stockService->decrease($item->item_id, $delta);
        } elseif ($delta < 0) {
            // fewer items sent, return stock
            $this->stockService->increase($item->item_id, abs($delta));
        }
    }

    public function deleted(DeliveryOrderItem $item): void
    {
        // If the delivery item is removed/cancelled, return stock
        $this->stockService->increase($item->item_id, $item->jumlah);
    }
}
