<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateYearColumnInDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            // Cambiar el tipo del campo 'year' a string con un máximo de 9 caracteres
            $table->string('year', 9)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            // Si quieres revertir el cambio, puedes volver a hacerlo numérico o como estaba antes
            $table->integer('year')->nullable()->change();
        });
    }
}
