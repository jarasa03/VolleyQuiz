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
        Schema::create('question_tag', function (Blueprint $table) {
            $table->foreignId('question_id')->constrained('questions');  // FK a la tabla questions
            $table->foreignId('tag_id')->constrained('tags');  // FK a la tabla tags
            $table->primary(['question_id', 'tag_id']);  // Clave primaria compuesta
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_tag');
    }
};
