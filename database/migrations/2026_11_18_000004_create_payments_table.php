<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained()->cascadeOnDelete();
            $table->string('periode_bulan'); // contoh: "November 2026"
            $table->unsignedInteger('nominal');
            $table->string('bukti_transfer')->nullable();
            $table->enum('status', ['belum_bayar', 'verifikasi', 'lunas'])->default('belum_bayar');
            $table->timestamps();

            // satu kontrak hanya punya satu tagihan per periode
            $table->unique(['lease_id', 'periode_bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
