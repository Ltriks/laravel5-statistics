<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserStatisticsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_statistics', function(Blueprint $table)
		{
			$table->increments('id');
			$table->char('date');
		    $table->integer('user_total');
		    $table->integer('user_from_mobile');
		    $table->integer('user_from_wx');
		    $table->integer('user_from_qq');
		    $table->integer('user_from_weibo');
		    $table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_statistics');
	}

}
