<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLetiDetiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leti_deti', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('from_user_id');
            $table->unsignedInteger('to_user_id');
            $table->double('amount', '15','2');
            $table->double('from_user_balance', '15','2');
            $table->double('to_user_balance', '15','2');
            $table->unsignedInteger('sub_game_id')->nullable();
            /*$table->unsignedInteger('runner_id')->nullable();
            $table->unsignedInteger('game_id')->nullable();*/
            $table->enum('type', ['Limit', 'CRDR']);
            $table->string('remark')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leti_deti');
    }
}
