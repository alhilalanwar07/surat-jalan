<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_sj')->unique();
            $table->date('tanggal')->index();
            $table->unsignedBigInteger('purpose_id')->index();
            $table->foreign('purpose_id', 'fk_delivery_orders_purpose_id')->references('id')->on('purposes')->onDelete('cascade');
            $table->string('nomor_po')->nullable()->index();
            $table->string('nama_sopir')->nullable();
            $table->string('nomor_kendaraan')->nullable();
            $table->string('purpose')->nullable(); // Field tambahan
            $table->string('alokasi')->nullable(); // Field tambahan
            $table->string('dokumentasi')->nullable(); // Field tambahan (path file)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};
