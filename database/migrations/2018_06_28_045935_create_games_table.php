<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tournament_id');
            $table->string('name');
            $table->unsignedInteger('winner_runner_id')->nullable();
            $table->unsignedInteger('score')->nullable();
            $table->dateTime('game_date');
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->string('market_id');
            $table->string('event_id');
            $table->enum('status', ['Active', 'Inactive','Completed']);
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
        Schema::dropIfExists('games');
    }
}
