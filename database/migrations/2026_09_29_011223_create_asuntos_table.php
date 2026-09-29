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
        Schema::create('asuntos', function (Blueprint $table) {
            $table->id();

            $table->string('titulo', 150)->unique();
            $table->string('slug', 180)->unique();

            $table->string('password_hash');

            $table->longText('clave_password_cifrada');
            $table->longText('clave_admin_cifrada')->nullable();

            $table->timestamp('expira_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asuntos');
    }
};
