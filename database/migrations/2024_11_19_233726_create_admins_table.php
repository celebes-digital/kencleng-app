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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();

            $table->char('nama', 50);
            $table->enum('level', [
                'superadmin',
                'principal',
                'direktur',
                'admin_wilayah',
                'manajer',
                'admin',
                'supervisor'
            ]);
            $table->string('telepon')->nullable();

            /** 
             * User ID must be set to nullable because that is default behaviour from filament, set the belongs to relationship to null
             * https://filamentphp.com/docs/3.x/forms/advanced#saving-data-to-a-belongsto-relationship
             * This use when make admin (admin resource form)
            */
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();

            $table->foreignId('area_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('cabang_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('wilayah_id')->nullable()->constrained()->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
