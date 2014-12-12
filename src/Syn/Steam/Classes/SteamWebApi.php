<?php namespace Syn\Steam\Classes;

use Config;
use GuzzleHttp\Client;
use Syn\Framework\Exceptions\MissingConfigurationException;
use Syn\Framework\Exceptions\MissingImplementationException;
use Syn\Framework\Exceptions\MissingMethodException;
use Syn\Framework\Exceptions\UnexpectedResultException;

class SteamWebApi
{
	/**
	 * Api key to connect with
	 * @see http://steamcommunity.com/dev/apikey
	 * @var string
	 */
	protected $api_key;
	/**
	 * Base uri for loading api
	 * @var string
	 */
	protected $base_uri;
	/**
	 * Path in base_uri to find method list
	 * @var string
	 */
	protected $method_list_path;

	/**
	 * Guzzle http/rest client
	 * @var \GuzzleHttp\Client
	 */
	protected $guzzle;

	/**
	 * Api valid methods/interfaces
	 * @var array|mixed
	 */
	protected $_interfaces = [];

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this -> api_key = Config::get('steam::api_key');
		if(!$this -> api_key)
			throw new MissingConfigurationException('Api key is highly recommended, so we require it (http://steamcommunity.com/dev/apikey)');

		$this -> base_uri = Config::get('steam::base_uri');
		if(!$this -> base_uri)
			throw new MissingConfigurationException('No steam base url configured');

		$this -> method_list_path = Config::get('steam::method_list_path');
		if(!$this -> method_list_path)
			throw new MissingConfigurationException('No steam method list path configured');

		$this -> guzzle = new Client([
			'base_url' => $this -> base_uri,
			'defaults' => [
				'headers' => [
					'User-Agent' => str_replace(" ", "-", Config::get('app.name')),
					'Accept' => 'application/json'
				]
			]
		]);

		$request = $this -> guzzle -> get($this -> method_list_path);

		if($request->getStatusCode() != 200)
			throw new UnexpectedResultException("Incorrect steam result for method list {$request->getReasonPhrase()}");

		$this -> _interfaces = json_decode($request->getBody());
	}

	/**
	 * Searches Api definition for correct method
	 * @info if multiple finds highest version
	 * @param $snake_case_method
	 * @return null
	 */
	protected function findExplicitMethod($snake_case_method)
	{
		$version = null;
		$select = null;
		foreach($this -> _interfaces -> apilist -> interfaces as $interface)
		{
			foreach($interface -> methods as $method)
			{
				if(snake_case($method -> name) == $snake_case_method)
				{
					// always use last version
					if(!$version || $version < $method -> version)
					{
						$version = $method -> version;
						$select = compact('interface', 'method');
					}
				}
			}
		}

		return $select;
	}

	protected function createMethodRequest($apiMethod, $arguments)
	{
		$get_arguments = ['key' => $this -> api_key];
		$method = array_get($apiMethod, 'method');
		$interface = array_get($apiMethod, 'interface');

		$path = sprintf("%s/%s/v%04d/", $interface -> name, $method -> name, $method -> version);
		$http_method = strtolower($method -> httpmethod);
		switch($http_method)
		{
			case 'get':
				$request = $this -> guzzle -> {$http_method}($path, [
					'query' => array_merge($get_arguments, $arguments)
				]);
				break;
			default:
				throw new MissingImplementationException("'{$http_method}' not configured in api client");
		}
		return json_decode($request->getBody());
	}

	/**
	 * Loads api call or class method
	 * @param $method
	 * @param $arguments
	 * @return mixed
	 * @throws \Syn\Framework\Exceptions\MissingMethodException
	 */
	public function __call($method, $arguments)
	{
		// default behavior
		if(method_exists($this, $method))
			return call_user_func([$this, $method], $arguments);

		if(($interface = $this -> findExplicitMethod($method)))
		{
			$body = $this -> createMethodRequest($interface, $arguments);
		}
		else
			throw new MissingMethodException("No method found for {$method}");

		return $body;
	}
}