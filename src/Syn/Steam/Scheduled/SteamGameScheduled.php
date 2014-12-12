<?php namespace Syn\Steam\Scheduled;

use File;
use Indatus\Dispatcher\Scheduling\Schedulable;
use Indatus\Dispatcher\Scheduling\ScheduledCommand;
use Syn\Gamer\Models\Gamer;
use Syn\Steam\Classes\SteamWebApi;
use Syn\Steam\Models\SteamGame;

class SteamGameScheduled extends ScheduledCommand
{
	protected $name = 'steam:games';
	protected $description = 'Loads all new games';


	public function fire()
	{
		$api = new SteamWebApi();
		$body = $api -> get_app_list();

		// load a valid list from the api
		foreach($body -> applist -> apps as $app)
		{
			// previous experiences showed that somehow sometimes Id was 0
			if(empty($app->appid))
				continue;
			$game = SteamGame::findOrNew($app -> appid);
			if($game->exists)
				continue;
			$game -> id = $app -> appid;
			$game -> name = $app -> name;
			$game->save();
		}

		// now loop through the users to gather additional information about the games
		$gamers = Gamer::has('steamProfile')->get();
		foreach($gamers as $gamer)
		{
			try {
				$xml = simplexml_load_file($gamer -> steamProfile -> steamXmlGamesLink);
			} catch(\Exception $e)
			{
				$this -> error("Failed on {$gamer->publishedName}: {$e->getMessage()}");
				// skip on error
				continue;
			}

			if(empty($gamer -> steamProfile -> id_64))
				$gamer -> steamProfile -> id_64 = (string) $xml -> steamID64;

			$gamer->save();

			foreach($xml -> games -> game as $i => $game)
			{
				$appId = (string) $game->appID;
				// skip zero or null or 0 app Id's probably failures
				if(empty($appId) || File::exists(public_path("/media/steam/games/{$appId}.jpg")))
					continue;

				// copy file if exists
				if(!empty((string) $game -> logo))
					File::copy((string) $game -> logo, public_path("/media/steam/games/{$appId}.jpg"));
			}
		}

	}

	/**
	 * @param Schedulable $scheduler
	 * @return Schedulable|\Indatus\Dispatcher\Scheduling\Schedulable[]
	 */
	public function schedule(Schedulable $scheduler)
	{
		return $scheduler
			-> daily()
			-> hours(4)
			-> minutes(0);
	}

	/**
	 * @return array|string
	 */
	public function environment()
	{
		return ['production'];
	}

	/**
	 * @return bool
	 */
	public function runInMaintenanceMode()
	{
		return false;
	}
}