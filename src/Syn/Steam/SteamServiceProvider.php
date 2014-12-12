<?php namespace Syn\Steam;

use Illuminate\Support\ServiceProvider;
use Syn\Steam\Models\GamerSteamProfile;
use Syn\Steam\Observers\GamerSteamProfileObserver;

class SteamServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;


	public function boot()
	{

		$this -> package('syn/steam');

		GamerSteamProfile::observe(new GamerSteamProfileObserver);

		$this->app->bindIf('command.syn.steam.games', function ($app) {
			return new Scheduled\SteamGameScheduled();
		});
		$this->commands(
			'command.syn.steam.games'
		);

		include __DIR__ . '/../../routes.php';
	}
	/**
	 * ice provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
