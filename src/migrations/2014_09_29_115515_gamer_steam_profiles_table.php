<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class GamerSteamProfilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('gamer_steam_profiles', function($table)
		{
			$table -> bigIncrements('id');
			$table -> string('url');
			$table -> string('id_64',255) -> nullable();
			$table -> timestamp('checked_at') -> nullable();

			$table -> softDeletes();
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
		Schema::drop('gamer_steam_profiles');
	}

}
