<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCtrlCodiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ctrl_codi', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('Codi',11);
            $table->dateTime('Data');
            $table->integer('DNI');
            $table->boolean('Assistix');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ctrl_codi');
    }
}
