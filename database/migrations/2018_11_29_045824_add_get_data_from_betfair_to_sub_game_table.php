<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGetDataFromBetfairToSubGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_game', function (Blueprint $table) {
            $table->enum('get_data_from_betfair', ['Yes', 'No'])->default('No')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_game', function (Blueprint $table) {
            $table->dropColumn('get_data_from_betfair');
        });
    }
}
