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
        Schema::create('questions', function (Blueprint $table) {
            $table->id(); // PK y AUTOINCREMENT
            $table->text('question_text'); // El enunciado de la pregunta
            $table->string('question_type');  // Tipo de pregunta (opción múltiple, verdadero/falso)
            $table->foreignId('category_id')->constrained('categories');  // FK a la tabla categories
            $table->timestamps(); // Crea los campos 'created_at' y 'updated_at' de tipo TIMESTAMP
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
