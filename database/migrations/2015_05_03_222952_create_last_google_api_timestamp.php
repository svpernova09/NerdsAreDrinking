<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLastGoogleApiTimestamp extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('last_google_api_timestamp', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('timestamp');
            $table->timestamps();
        });

        $insert = [
            Carbon\Carbon::now()->timestamp,
            Carbon\Carbon::now(),
            Carbon\Carbon::now()
        ];
        DB::insert("insert into last_google_api_timestamp (timestamp, created_at, updated_at) values (?, ?, ?)", $insert);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('last_google_api_timestamp');
	}

}
