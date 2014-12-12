<?php namespace Syn\Steam\Controllers;

use Syn\Framework\Abstracts\Controller;
use Syn\Steam\Classes\OpenIdAuthentication;
use Syn\Steam\Models\GamerSteamProfile;

class SteamOpenIdController extends Controller
{

	const STEAM_OPEN_ID_PROVIDER = 'https://steamcommunity.com/openid';



	public function signedIn($gamer, $name = null)
	{

		$auth = new OpenIdAuthentication($gamer);

		$response = $auth->getResponse();

		switch($response->status)
		{
			case Auth_OpenID_SETUP_NEEDED:
			case Auth_OpenID_PARSE_ERROR:
			case Auth_OpenID_FAILURE:
				return $gamer->redirectEdit->with('error', $response->message);
				break;
			case Auth_OpenID_CANCEL:
				return $gamer->redirectEdit;
				break;
			case Auth_OpenID_SUCCESS:
		}

		$steam_url = str_replace("http://steamcommunity.com/openid/id/", "http://steamcommunity.com/profiles/", $response->identity_url);

		$check = GamerSteamProfile::where('url', $steam_url) -> count() >= 1;
		if($check)
			return $gamer->redirectEdit->with('error', trans('gamer.steam-taken'));

		if($gamer -> steamProfile)
			$profile = $gamer -> steamProfile;
		else
		{
			$profile = new GamerSteamProfile;
			$profile -> id = $gamer -> id;
		}

		$profile -> url = $steam_url;
		$profile -> save();

		return $gamer -> redirectEdit;

	}
}