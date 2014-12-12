<?php namespace Syn\Steam\Models;

use File;
use Syn\Framework\Abstracts\Model;

class SteamGame extends Model
{
	public $_validation = [
		'name' => ['required','disabled'],
		'selectable' => ['boolean'],
	];
	public $_types = [
		'name' => 'text',
		'selectable' => 'toggle'
	];

	public function getImageUriAttribute()
	{
		if(!$this -> exists)
			return null;
		$path = sprintf("/%s/%s.jpg", public_path('media/steam/games'), $this -> id);
		$relative = sprintf("/%s/%s.jpg", 'media/steam/games', $this -> id);
		return File::exists($path) ? $relative : null;
	}

	public function scopeSelectable($query)
	{
		return $query -> where('selectable', true);
	}
}