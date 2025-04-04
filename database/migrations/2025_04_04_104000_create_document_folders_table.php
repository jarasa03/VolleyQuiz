<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('document_folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('section_id')->constrained('document_sections')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('document_folders')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_folders');
    }
};
