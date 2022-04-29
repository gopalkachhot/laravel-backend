<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsInUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table){
            $table->double('limit','15','2')->after('expose');
            $table->double('used_limit','15','2')->after('limit');
            $table->double('expense','15','2')->after('used_limit');
            $table->double('upper_level_expense','15','2')->default(0)->after('expense');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table){
            $table->dropColumn('limit');
            $table->dropColumn('used_limit');
            $table->dropColumn('expense');
            $table->dropColumn('upper_level_expense');
        });
    }
}
