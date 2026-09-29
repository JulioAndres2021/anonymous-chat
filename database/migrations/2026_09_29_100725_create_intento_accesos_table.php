<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('intentos_acceso', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asunto_id')
                ->constrained('asuntos')
                ->cascadeOnDelete();

            $table->text('ip_cifrada')->nullable();

            $table->string('ip_hash', 64);

            $table->string('sistema_operativo', 100)->nullable();

            $table->string('navegador', 100)->nullable();

            $table->text('user_agent')->nullable();

            $table->string('pais', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('ciudad', 100)->nullable();

            $table->unsignedInteger('intentos')->default(0);

            $table->timestamp('ultimo_intento_at')->nullable();

            $table->timestamp('bloqueado_hasta')->nullable();

            $table->timestamps();

            $table->unique([
                'asunto_id',
                'ip_hash',
            ]);

            $table->index('bloqueado_hasta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('intentos_acceso');
    }
};
