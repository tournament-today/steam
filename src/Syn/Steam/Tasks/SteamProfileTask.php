<?php namespace Syn\Steam\Tasks;

use App;
use Carbon\Carbon;
use File;
use Syn\Framework\Exceptions\MissingParameterException;

class SteamProfileTask
{
	public function readXmlProfile($job, $data)
	{
		$id = array_get($data, 'id', null);
		if(empty($id))
			throw new MissingParameterException('No profile Id given');

		$gamer = App::make('Syn\Gamer\Interfaces\GamerRepositoryInterface') -> findById($id);
		if(!$gamer)
			throw new MissingParameterException('No gamer found');

		if(!$gamer -> steamProfile)
			throw new MissingParameterException('No gamer steam profile found');

		$xml = simplexml_load_file($gamer -> steamProfile -> steamXmlProfileLink);

		// store the id 64
		$gamer -> steamProfile -> id_64 = (string) $xml -> steamID64;
		$gamer -> steamProfile -> created_at = Carbon::parse((string) $xml -> memberSince);
		$gamer -> steamProfile -> save();

		File::put(storage_path("steam_profile/{$gamer -> steamProfile -> id_64}.xml"), $xml);

		if($xml -> vacBanned)
		{
			// TODO something in the future
		}

		if($xml -> avatarFull)
			File::copy((string) $xml->avatarFull, public_path("/media/gamers/{$gamer->id}.jpg"));

		$job -> delete();
	}
}