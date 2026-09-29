<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asunto_id')
                ->constrained('asuntos')
                ->cascadeOnDelete();

            $table->longText('contenido_cifrado');

            $table->text('ip_cifrada')->nullable();

            $table->string('sistema_operativo', 100)->nullable();
            $table->string('navegador', 100)->nullable();

            $table->text('user_agent')->nullable();

            $table->string('pais', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('ciudad', 100)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['asunto_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes');
    }
};
