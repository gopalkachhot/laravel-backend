<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMaxStackInSubGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_game', function (Blueprint $table){
            $table->double('max_stack',getenv('MAX_STACK_LIMIT'),'2')->nullable()->after('result');
            $table->double('max_stack_amount',getenv('MAX_STACK_LIMIT'),'2')->nullable()->after('max_stack');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_game', function (Blueprint $table){
            $table->dropColumn('max_stack');
            $table->dropColumn('max_stack_amount');
        });
    }
}
