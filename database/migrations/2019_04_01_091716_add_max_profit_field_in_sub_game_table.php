<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMaxProfitFieldInSubGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_game', function (Blueprint $table) {
            $table->double('max_profit',15,2)->nullable()->after('max_stack_amount');
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
            $table->dropColumn('max_profit');
        });
    }
}
