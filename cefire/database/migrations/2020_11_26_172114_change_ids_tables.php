<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIdsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('centres', function (Blueprint $table) {
            //
            $table->renameColumn('ass_id', 'user_id');

        });
        Schema::table('compensa', function (Blueprint $table) {
            //
            $table->renameColumn('ass_id', 'user_id');

        });
        Schema::table('curs', function (Blueprint $table) {
            //
            $table->renameColumn('ass_id', 'user_id');

        });
        Schema::table('guardia', function (Blueprint $table) {
            //
            $table->renameColumn('ass_id', 'user_id');

        });
        Schema::table('notificacions', function (Blueprint $table) {
            //
            $table->renameColumn('ass_id', 'user_id');

        });
        Schema::table('permis', function (Blueprint $table) {
            //
            $table->renameColumn('ass_id', 'user_id');

        });
        Schema::table('visita', function (Blueprint $table) {
            //
            $table->renameColumn('ass_id', 'user_id');

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
    }
}
