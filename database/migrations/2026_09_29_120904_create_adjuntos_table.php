<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adjuntos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mensaje_id')
                ->constrained('mensajes')
                ->cascadeOnDelete();

            $table->text('nombre_original_cifrado');

            $table->string('ruta', 255)->unique();

            $table->string('mime', 100);

            $table->unsignedBigInteger('tamano');

            $table->timestamps();
            $table->softDeletes();

            $table->index('mensaje_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adjuntos');
    }
};
