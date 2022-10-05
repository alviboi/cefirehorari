<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitesCentresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visites_centres', function (Blueprint $table) {
            $table->integer('Total')->nullable();
            $table->integer('Definitius')->nullable();
            $table->integer('FuncPract')->nullable();
            $table->text('CFC')->nullable();
            $table->text('CAF')->nullable();
            $table->text('LiniaPedagogica')->nullable();
            $table->text('ProjectesDestacables')->nullable();
            $table->integer('iMoute')->nullable();
            $table->integer('Erasmus')->nullable();
            $table->integer('PIIE')->nullable();
            $table->text('AltresProjectes')->nullable();
            $table->text('Impacte')->nullable();
            $table->text('ParticipacioAF')->nullable();
            $table->text('FIPrimeresImpr')->nullable();
            $table->text('FIDificultatsTrobades')->nullable();
            $table->text('FIIDesenvolupament')->nullable();
            $table->text('FIIDificultatsTroben')->nullable();
            $table->text('Espais')->nullable();
            $table->dateTime('Data');
            $table->integer('user_id')->nullable();
            $table->integer('Codi')->index();
            $table->primary(array('Codi', 'Data'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visites_centres');
    }
}
