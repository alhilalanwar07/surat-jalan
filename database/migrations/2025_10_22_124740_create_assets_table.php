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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('project')->nullable();
            $table->string('kode_lot')->nullable();
            $table->string('kode_cat')->nullable();
            $table->string('initial_location')->nullable();
            $table->string('area')->nullable();
            $table->string('item_category')->nullable();
            $table->string('kode')->unique();
            $table->string('item_name');
            $table->string('merk')->nullable();
            $table->unsignedInteger('qty')->default(1);
            $table->string('uom')->nullable(); // Unit of Measure (Ea, Unit, dll)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
