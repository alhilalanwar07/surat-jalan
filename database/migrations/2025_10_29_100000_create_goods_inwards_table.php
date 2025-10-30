<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_inwards', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->nullable()->unique();
            $table->date('tanggal')->index();
            $table->json('attachments')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_inwards');
    }
};
