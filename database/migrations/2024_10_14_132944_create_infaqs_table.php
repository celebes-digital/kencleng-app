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
        Schema::create('infaqs', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->date('tgl_transaksi');
            $table->integer('jumlah');
            $table->string('uraian')->default('Pemasukan dana kencleng Nomor');
            $table->string('sumber_dana')->default('Kenceleng');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infaqs');
    }
};
