<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderInListFieldInSubGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_game', function (Blueprint $table) {
            $table->integer('order_in_list')->default(0)->after('get_data_from_betfair');
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
            $table->dropColumn('order_in_list');
        });
    }
}
