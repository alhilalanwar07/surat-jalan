<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use App\Models\StockMovement;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_increase_updates_item_and_creates_stock_movement_with_reference()
    {
        // migrate and prepare
        $user = User::factory()->create();
        $this->actingAs($user);

        $item = Item::create([
            'kode' => 'kd-0001',
            'nama' => 'Test Item',
            'satuan' => 'pcs',
            'stok' => 0,
        ]);

        $service = new StockService();
        $service->increase($item->id, 5, 'goods_inwards', 123, 'Penerimaan barang test');

        $item->refresh();
        $this->assertEquals(5, $item->stok);

        $movement = StockMovement::where('item_id', $item->id)->first();
        $this->assertNotNull($movement);
        $this->assertEquals(5, $movement->qty);
        $this->assertEquals('in', $movement->movement_type);
        $this->assertEquals('goods_inwards', $movement->reference_table);
        $this->assertEquals(123, $movement->reference_id);
        $this->assertEquals('Penerimaan barang test', $movement->note);
        $this->assertEquals($user->id, $movement->user_id);
    }
}
