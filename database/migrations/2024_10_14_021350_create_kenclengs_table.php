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
        Schema::create('kenclengs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_kencleng_id')->constrained('batch_kenclengs');
            $table->char('no_kencleng', 10);
            $table->string('qr_image', 100);
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kenclengs');
    }
};
