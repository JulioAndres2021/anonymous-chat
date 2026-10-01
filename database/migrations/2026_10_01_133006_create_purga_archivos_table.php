<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'purgas_archivos',
            function (Blueprint $table) {
                $table->id();

                $table->string(
                    'disk',
                    50
                )->default('local');

                $table->string('ruta')
                    ->unique();

                $table->unsignedInteger(
                    'intentos'
                )->default(0);

                $table->text(
                    'ultimo_error'
                )->nullable();

                $table->timestamp(
                    'purgado_at'
                )->nullable();

                $table->timestamps();

                $table->index(
                    'purgado_at'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'purgas_archivos'
        );
    }
};