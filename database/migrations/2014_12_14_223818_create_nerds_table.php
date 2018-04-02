<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNerdsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nerds', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('twitter');
            $table->timestamps();
        });

        $insert = ['thirstyrunner', 'thirstyrunner'];
        DB::insert("insert into nerds (name, twitter) values (?, ?)", $insert);
        $insert = ['svpernova09', 'svpernova09'];
        DB::insert("insert into nerds (name, twitter) values (?, ?)", $insert);
        $insert = ['markonthebluffs', 'markonthebluffs'];
        DB::insert("insert into nerds (name, twitter) values (?, ?)", $insert);
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('nerds');
    }
}
