<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tags', function (Blueprint $table) {
            // Añadimos la columna color, sin valor por defecto y permitiendo nulos.
            $table->string('color', 7)->nullable()->after('name');
        });
    }

    public function down()
    {
        Schema::table('tags', function (Blueprint $table) {
            // Si se hace rollback, eliminamos la columna color
            $table->dropColumn('color');
        });
    }
};
