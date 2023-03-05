<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddJustificacioToBorsasolicituds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('borsa_solicituds', function (Blueprint $table) {
            //
            $table->integer('minuts25')->default(0);
            $table->integer('minuts2')->default(0);
            $table->text("justificacio25");
            $table->text("justificacio2");
            $table->text("justificacio")->nullable()->change();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('borsa_solicituds', function (Blueprint $table) {
            //
        });
    }
}
