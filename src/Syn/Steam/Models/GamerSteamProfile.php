<?php namespace Syn\Steam\Models;

use Syn\Framework\Abstracts\Model;

class GamerSteamProfile extends Model
{

	/**
	 * @var array
	 */
	public $_validation = [
		'url' => [
			'required',
			'regex:/^https?:\/\/(www\.)?steamcommunity\.com\/(id\/([a-zA-Z0-9_-]+)|profiles\/([0-9]+))\/?$/i'
		],
	];
	/**
	 * @var array
	 */
	public $_validation_messages = [
		'url.regex' => 'gamer.steam-id-error'
	];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function gamer()
	{
		return $this -> belongsTo('Syn\Gamer\Models\Gamer', 'id');
	}

	/**
	 * Steam get profile information from steam
	 */
	public function getSteamXmlProfileLinkAttribute()
	{
		return sprintf("%s?xml=1", $this -> url);
	}

	public function getSteamXmlGamesLinkAttribute()
	{
		return sprintf('%s/games?xml=1', $this -> url);
	}

	public function getOnlineAttribute()
	{
		return;
	}

	/**
	 * @return null|string
	 * @see https://developer.valvesoftware.com/wiki/SteamID
	 *      $w is the resulting id 64
	 * 		$y is the modulus of the id 64
	 *      $z is the last part of the steam id
	 */
	public function getSteamIdAttribute()
	{
		if(!$this->id_64 && $this->url && preg_match('/([0-9]+)$/', $this->url,$m))
		{
			$this->id_64 = $m[1];
			$this->save();
		}

		if(!$this->id_64)
			return null;

		$w = $this -> id_64;

		$y = $w % 2;

		$z = ($w - 76561197960265728 - $y)/2;
		return "STEAM_0:{$y}:{$z}";
	}
}