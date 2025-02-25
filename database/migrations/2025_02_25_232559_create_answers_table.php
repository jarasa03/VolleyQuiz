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
        Schema::create('answers', function (Blueprint $table) {
            $table->id(); // PK y AUTOINCREMENT
            $table->foreignId('question_id')->constrained('questions');  // FK a la tabla questions
            $table->text('answer_text');  // El texto de la respuesta
            $table->boolean('is_correct');  // Marca si la respuesta es correcta
            $table->timestamps(); // Crea los campos 'created_at' y 'updated_at' de tipo TIMESTAMP
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
