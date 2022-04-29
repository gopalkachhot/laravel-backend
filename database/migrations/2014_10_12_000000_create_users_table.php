<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('domain');
            $table->string('name');
            $table->string('user_name');
            $table->string('email')->nullable();
            $table->string('password');
            $table->string('mobile');
            $table->string('city');
            $table->float('partnership')->default(100);
            //$table->string('balance')->nullable();
            $table->string('expose')->nullable();
            $table->float('extra_delay')->nullable();
            $table->float('min_bet','15','2')->nullable();
            $table->float('max_bet','15','2')->nullable();
            $table->float('expose_limit','15','2')->nullable();
            $table->enum('is_admin', ['Yes', 'No'])->default('Yes');
            $table->enum('is_betting_now', ['Yes', 'No'])->default('No');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
