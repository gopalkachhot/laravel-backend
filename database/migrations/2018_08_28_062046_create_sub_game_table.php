<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_game', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('game_id')->unsigned();
            $table->integer('bookie_id')->nullable();
            $table->enum('type', ['Match', 'OddEven', 'Fancy', 'Dabba', 'Toss']);
            $table->string('name');
            $table->longText('message')->nullable();
            $table->enum('status', ['Ball Running', 'Suspended', 'Active','Inactive','Done'])->default('Inactive');
            $table->longText('cards')->nullable();
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
        Schema::dropIfExists('sub_game');
    }
}
