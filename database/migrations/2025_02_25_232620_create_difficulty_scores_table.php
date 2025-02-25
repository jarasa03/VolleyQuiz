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
        Schema::create('difficulty_scores', function (Blueprint $table) {
            $table->id(); // PK y AUTOINCREMENT
            $table->foreignId('user_id')->constrained('users');  // FK a la tabla users
            $table->foreignId('test_id')->constrained('tests');  // FK a la tabla tests
            $table->string('difficulty');  // Dificultad (bajo, medio, alto)
            $table->integer('score');  // Puntuación obtenida en el test
            $table->timestamps(); // Crea los campos 'created_at' y 'updated_at' de tipo TIMESTAMP
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('difficulty_scores');
    }
};
