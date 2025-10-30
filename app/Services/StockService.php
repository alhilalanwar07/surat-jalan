<?php

namespace App\Services;

use App\Models\Item;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Support\Facades\Auth;

class StockService
{
    /**
     * Increase stock for an item safely using DB transaction and row lock
     * @param int $itemId
     * @param int $qty
     * @return Item
     */
    public function increase(int $itemId, int $qty, ?string $referenceTable = null, ?int $referenceId = null, ?string $note = null): Item
    {
        if ($qty <= 0) {
            throw new RuntimeException('Quantity must be greater than zero');
        }

    return DB::transaction(function () use ($itemId, $qty, $referenceTable, $referenceId, $note) {
            // lock the row for update to avoid race conditions
            $item = Item::where('id', $itemId)->lockForUpdate()->firstOrFail();
            $item->stok = $item->stok + $qty;
            $item->save();
            // record stock movement
            StockMovement::create([
                'item_id' => $item->id,
                'qty' => $qty,
                'movement_type' => 'in',
                'reference_table' => $referenceTable,
                'reference_id' => $referenceId,
                'user_id' => Auth::id(),
                'note' => $note,
            ]);

            return $item;
        });
    }

    /**
     * Decrease stock safely. Throws if insufficient stock.
     * @param int $itemId
     * @param int $qty
     * @return Item
     */
    public function decrease(int $itemId, int $qty, ?string $referenceTable = null, ?int $referenceId = null, ?string $note = null): Item
    {
        if ($qty <= 0) {
            throw new RuntimeException('Quantity must be greater than zero');
        }

    return DB::transaction(function () use ($itemId, $qty, $referenceTable, $referenceId, $note) {
            $item = Item::where('id', $itemId)->lockForUpdate()->firstOrFail();
            if ($item->stok < $qty) {
                throw new RuntimeException('Insufficient stock for item ID ' . $itemId);
            }
            $item->stok = $item->stok - $qty;
            $item->save();
            // record stock movement
            StockMovement::create([
                'item_id' => $item->id,
                'qty' => $qty,
                'movement_type' => 'out',
                'reference_table' => $referenceTable,
                'reference_id' => $referenceId,
                'user_id' => Auth::id(),
                'note' => $note,
            ]);

            return $item;
        });
    }
}
