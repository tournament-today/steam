<?php
Route::group(['namespace' => 'Syn\Steam\Controllers'], function()
{
	Route::group([
		'before' 	=> ['auth'],
	], function()
	{

//		Route::any('/steam/sign-in', [
//			'as' => 'Steam@signIn',
//			'uses' => 'SteamOpenIdController@signIn'
//		]);
		Route::any('/{gamer}/{name}/steam/signed-in', [
			'as' => 'Steam@signedIn',
			'uses' => 'SteamOpenIdController@signedIn'
		]);
	});
});