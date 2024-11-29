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
            $table->id();

            $table->foreignId('distribusi_id')->nullable()->constrained('distribusi_kenclengs')->cascadeOnDelete();

            $table->foreignId('area_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('cabang_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('wilayah_id')->nullable()->constrained()->onDelete('set null');

            $table->char('nama_donatur', 100);
            $table->date('tgl_transaksi');
            $table->integer('jumlah_donasi');
            $table->string('uraian')->nullable();
            $table->string('sumber_dana')->default('Kencleng');
            $table->string('metode_donasi')->default('Tunai');

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
