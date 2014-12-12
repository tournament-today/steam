<?php namespace Syn\Steam\Classes;

use Session;
use Auth_Yadis_PHPSession;

class OpenIdSession extends Auth_Yadis_PHPSession
{
	function set($name, $value)
	{
		Session::put($name, $value);
	}

	function get($name, $default = null)
	{
		return Session::get($name, $default);
	}

	function del($name)
	{
		Session::forget($name);
	}

	function contents()
	{
		return Session::all();
	}

}