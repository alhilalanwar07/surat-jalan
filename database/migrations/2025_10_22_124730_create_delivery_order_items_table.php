<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('delivery_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_order_id')->index();
            $table->unsignedBigInteger('item_id')->index();
            $table->unsignedInteger('jumlah');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('delivery_order_id', 'fk_delivery_order_items_delivery_order_id')->references('id')->on('delivery_orders')->onDelete('cascade');
            $table->foreign('item_id', 'fk_delivery_order_items_item_id')->references('id')->on('items')->onDelete('cascade');

            // Uncomment if you want to prevent duplicate items per delivery order
            // $table->unique(['delivery_order_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_order_items');
    }
};
