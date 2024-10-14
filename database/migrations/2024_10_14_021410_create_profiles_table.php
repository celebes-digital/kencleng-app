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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 255);
            $table->date('tgl_lahir');
            $table->char('nik', 16);
            $table->enum('kelamin', ['L', 'P']);
            $table->string('pekerjaan', 100);
            $table->string('alamat', 255);
            $table->string('kelurahan', 100);
            $table->string('kecamatan', 100);
            $table->string('kabupaten', 100);
            $table->string('provinsi', 100);
            $table->string('no_hp', 15);
            $table->string('no_wa', 15);
            $table->string('poto', 100);
            $table->string('poto_ktp', 100);
            $table->string('group', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
