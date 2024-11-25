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
        Schema::create('distribusi_kenclengs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kencleng_id')->constrained()->onDelete('cascade');
            $table->bigInteger('donatur_id')->unsigned()->nullable();
            $table->bigInteger('kolektor_id')->unsigned()->nullable();
            $table->bigInteger('distributor_id')->unsigned()->nullable();
            $table->decimal('geo_lat', 10, 8)->nullable();
            $table->decimal('geo_long', 11, 8)->nullable();
            $table->datetime('tgl_distribusi')->nullable();
            $table->datetime('tgl_aktivasi')->nullable();
            $table->date('tgl_batas_pengambilan')->nullable();
            $table->datetime('tgl_pengambilan')->nullable();
            $table->integer('jumlah')->unsigned()->nullable();
            $table->string('status')->default('distribusi');

            $table->foreignId('area_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('cabang_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('wilayah_id')->nullable()->constrained()->onDelete('set null');

            $table->foreign('donatur_id')
                    ->references('id')
                    ->on('profiles')
                    ->onDelete('set null');
            $table->foreign('kolektor_id')
                    ->references('id')
                    ->on('profiles')
                    ->onDelete('set null');
            $table->foreign('distributor_id')
                    ->references('id')
                    ->on('profiles')
                    ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distribusi_kenclengs');
    }
};
