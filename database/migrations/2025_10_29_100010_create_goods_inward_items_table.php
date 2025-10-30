<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_inward_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_inward_id')->constrained('goods_inwards')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->unsignedInteger('jumlah');
            $table->decimal('harga', 14, 2)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_inward_items');
    }
};
