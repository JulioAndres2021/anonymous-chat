<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mensajes', function (Blueprint $table) {
            $table->dropColumn([
                'pais',
                'provincia',
                'ciudad',
            ]);
        });

        Schema::table('intentos_acceso', function (Blueprint $table) {
            $table->dropColumn([
                'pais',
                'provincia',
                'ciudad',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('mensajes', function (Blueprint $table) {
            $table->string('pais', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('ciudad', 100)->nullable();
        });

        Schema::table('intentos_acceso', function (Blueprint $table) {
            $table->string('pais', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('ciudad', 100)->nullable();
        });
    }
};
