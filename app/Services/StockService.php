<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    /**
     * Increase stock for an item safely using DB transaction and row lock
     * @param int $itemId
     * @param int $qty
     * @return Item
     */
    public function increase(int $itemId, int $qty): Item
    {
        if ($qty <= 0) {
            throw new RuntimeException('Quantity must be greater than zero');
        }

        return DB::transaction(function () use ($itemId, $qty) {
            // lock the row for update to avoid race conditions
            $item = Item::where('id', $itemId)->lockForUpdate()->firstOrFail();
            $item->stok = $item->stok + $qty;
            $item->save();
            return $item;
        });
    }

    /**
     * Decrease stock safely. Throws if insufficient stock.
     * @param int $itemId
     * @param int $qty
     * @return Item
     */
    public function decrease(int $itemId, int $qty): Item
    {
        if ($qty <= 0) {
            throw new RuntimeException('Quantity must be greater than zero');
        }

        return DB::transaction(function () use ($itemId, $qty) {
            $item = Item::where('id', $itemId)->lockForUpdate()->firstOrFail();
            if ($item->stok < $qty) {
                throw new RuntimeException('Insufficient stock for item ID ' . $itemId);
            }
            $item->stok = $item->stok - $qty;
            $item->save();
            return $item;
        });
    }
}
