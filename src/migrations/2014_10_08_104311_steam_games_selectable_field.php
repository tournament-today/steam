<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SteamGamesSelectableField extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('steam_games', function($table)
		{
			$table -> boolean('selectable') -> default(false) -> after('name');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('steam_games', function($table)
		{
			$table -> dropColumn('selectable');
		});
	}

}
