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
        Schema::create('document_sections', function (Blueprint $table) {
            $table->id(); // PK y AUTOINCREMENT
            $table->string('name'); // Nombre de la sección (internacional, nacional, territorial)
            $table->timestamps(); // Crea los campos 'created_at' y 'updated_at' de tipo TIMESTAMP
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_sections');
    }
};
