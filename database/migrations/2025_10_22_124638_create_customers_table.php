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
        Schema::create('purposes', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->index();
            $table->text('alamat')->nullable();
            // use consistent column name and limit length for phone
            $table->string('no_telepon', 25)->nullable();
            $table->timestamps();
            // allow soft delete for recoverable customer records
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purposes');
    }
};
