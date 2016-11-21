<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddNameToLastTweetsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('last_tweets', function (Blueprint $table) {
            $table->string('name')->after('since_id');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('last_tweets', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
}
