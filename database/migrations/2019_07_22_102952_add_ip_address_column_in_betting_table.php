<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIpAddressColumnInBettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('betting', function (Blueprint $table) {
            $table->string('ip_address',45)->nullable();
            $table->text('browser_detail')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('betting', function (Blueprint $table) {
            $table->dropColumn('ip_address');
            $table->dropColumn('browser_detail');
        });
    }
}
