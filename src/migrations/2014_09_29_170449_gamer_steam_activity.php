<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GamerSteamActivity extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('gamer_steam_activities', function($table)
		{
			$table -> bigIncrements('id');
			$table -> bigInteger('gamer_id') -> unsigned;
			$table -> boolean('online');
			$table -> boolean('away');
			$table -> boolean('mobile');
			$table -> boolean('in_game');
			$table -> bigInteger('game_id') -> unsigned() -> nullable();

			$table -> timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('gamer_steam_activities');
	}

}
