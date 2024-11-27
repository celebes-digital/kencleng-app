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
        Schema::table('distribusi_kenclengs', function (Blueprint $table) {
            $table->enum('status_kelanjutan', ['lanjut_tetap', 'lanjut_pindah', 'tidak_lanjut'])->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('distribui_kenclengs', function (Blueprint $table) {
            $table->dropColumn('status_kelanjutan');
        });
    }
};
