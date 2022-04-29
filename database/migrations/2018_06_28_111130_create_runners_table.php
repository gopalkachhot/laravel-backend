<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRunnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('runners', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('game_id');
            $table->string('name');
            $table->integer('betfair_runner_id')->nullable();
            $table->enum('type', ['Match', 'OddEven', 'Fancy', 'Dabba', 'Toss']);
            $table->string('lay2');
            $table->string('lay2_value');
            $table->string('lay1');
            $table->string('lay1_value');
            $table->string('lay');
            $table->string('lay_value');
            $table->string('back');
            $table->string('back_value');
            $table->string('back1');
            $table->string('back1_value');
            $table->string('back2');
            $table->string('back2_value');
            $table->float('delay')->nullable();
            $table->integer('min_bet')->nullable();
            $table->integer('max_bet')->nullable();
            $table->float('extra_delay_rate')->nullable();
            $table->double('extra_delay', '8','2')->nullable();
            //$table->enum('status', ['Ball Running', 'Suspended', 'Active','Inactive'])->default('Inactive');
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
        Schema::dropIfExists('runners');
    }
}
