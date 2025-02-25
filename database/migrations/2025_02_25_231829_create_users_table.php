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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // PK y AUTOINCREMENT
            $table->string('name'); // Nickname
            $table->string('email')->unique(); // Email
            $table->string('password'); // Contraseña
            $table->timestamp('email_verified_at')->nullable(); // Crea un campo 'remember_token' para la funcionalidad de "Recordarme"
            $table->timestamps(); // Crea los campos 'created_at' y 'updated_at' de tipo TIMESTAMP
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};