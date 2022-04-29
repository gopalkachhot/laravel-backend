<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('betting', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('runner_id');
            $table->float('loss_amount');
            $table->float('win_amount');
            $table->enum('type', ['Back', 'Lay']);
            $table->float('rate');
            $table->float('value');
            $table->float('amount');
            $table->enum('is_in_unmatch', ['Yes', 'No'])->default('No');
            $table->dateTime('unmatch_to_match_time')->nullable()->default(null);
            $table->enum('bet_status', ['Pending', 'Completed']);
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
        Schema::dropIfExists('betting');
    }
}
