<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubGameidInGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('runners', function (Blueprint $table){
            $table->dropColumn(['game_id', 'type']);
            $table->unsignedInteger('sub_game_id')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('runners', function (Blueprint $table){
            $table->dropColumn('sub_game_id');
            $table->unsignedInteger('game_id')->after('id');
            $table->enum('type', ['Match', 'OddEven', 'Fancy', 'Dabba', 'Toss'])->after('game_id');
        });
    }
}
