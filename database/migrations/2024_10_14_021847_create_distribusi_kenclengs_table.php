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
            $table->date('tgl_distribusi')->nullable();
            $table->date('tgl_pengambilan')->nullable();
            $table->integer('jumlah')->unsigned()->nullable();
            $table->boolean('diterima')->default(false);

            $table->foreign('donatur_id')
                    ->references('id')
                    ->on('profiles')
                    ->onDelete('cascade');
            $table->foreign('kolektor_id')
                    ->references('id')
                    ->on('profiles')
                    ->onDelete('cascade');
            $table->foreign('distributor_id')
                    ->references('id')
                    ->on('profiles')
                    ->onDelete('cascade');
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
