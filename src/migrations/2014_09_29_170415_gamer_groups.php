<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GamerGroups extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('gamer_steam_groups', function($table)
		{
			$table -> bigIncrements('id');
			$table -> bigInteger('gamer_id') -> unsigned();
			$table -> bigInteger('group_id') -> unsigned();
			$table -> boolean('primary');
			$table -> timestamps();
			$table -> softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('gamer_steam_groups');
	}

}
