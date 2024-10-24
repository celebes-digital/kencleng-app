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
            $table->foreignId('distribusi_id')
                    ->constrained('distribusi_kenclengs')
                    ->cascadeOnDelete();

            $table->date('tgl_transaksi');
            $table->integer('jumlah_donasi');
            $table->string('uraian')->nullable();
            $table->string('sumber_dana')->default('Kencleng');
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
