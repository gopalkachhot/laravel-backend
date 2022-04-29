<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUpperLevelExpenseInLetiDetiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leti_deti', function (Blueprint $table) {
            $table->double('upper_level_expense','15','2')->default(0)->after('to_user_balance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leti_deti', function (Blueprint $table) {
            $table->dropColumn('upper_level_expense','15','2');
        });
    }
}
