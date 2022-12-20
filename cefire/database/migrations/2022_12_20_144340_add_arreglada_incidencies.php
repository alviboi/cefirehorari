<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArregladaIncidencies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('incidencies', function (Blueprint $table) {
            //
            $table->boolean('corregida')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('incidencies', function (Blueprint $table) {
            //
            $table->dropColumn('corregida');
        });
    }
    
}
