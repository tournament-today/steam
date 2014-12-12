<?php namespace Syn\Steam\Classes;

use Auth_OpenID_Consumer;
use Auth_OpenID_FileStore;
use Auth_OpenID_PAPE_Request;
use Auth_OpenID_SRegRequest;
use Config;
use Redirect;
use Route;
use Syn\Framework\Abstracts\Controller;
use Syn\Framework\Exceptions\UnexpectedResultException;
use Syn\Gamer\Models\Gamer;
use Syn\Steam\Classes\OpenIdSession;

class OpenIdAuthentication
{
	const STEAM_OPEN_ID_PROVIDER = 'https://steamcommunity.com/openid';


	public function __construct(Gamer $gamer)
	{
		$this -> gamer = $gamer;
		$this -> returnUrl = route('Steam@signedIn', ['gamer' => $this->gamer->id, 'name' => $this -> gamer -> linkName]);
	}

	public function begin()
	{
		$authenticationRequest = $this->getConsumer()->begin(static::STEAM_OPEN_ID_PROVIDER);
		if(!$authenticationRequest)
			throw new UnexpectedResultException("OpenID request to " . static::STEAM_OPEN_ID_PROVIDER . " failed");

		$signingRequest = Auth_OpenID_SRegRequest::build(['nickname','fullname','email','timezone','language']);

		if($signingRequest)
			$authenticationRequest->addExtension($signingRequest);

		$pape_request = new Auth_OpenID_PAPE_Request([
			PAPE_AUTH_MULTI_FACTOR_PHYSICAL,
			PAPE_AUTH_MULTI_FACTOR,
			PAPE_AUTH_PHISHING_RESISTANT
		]);
		if($pape_request)
			$authenticationRequest->addExtension($pape_request);

		// redirect to open Id
		if($authenticationRequest->shouldSendRedirect())
			throw new UnexpectedResultException("Redirection request received");
		else
			return $authenticationRequest->htmlMarkup(
				Config::get('app.url'),
				$this -> returnUrl
			);
	}

	public function getResponse()
	{
		return $this->getConsumer()->complete($this->returnUrl);
	}

	protected function getStore()
	{
		return new Auth_OpenID_FileStore(storage_path('open_id'));
	}

	protected function getConsumer()
	{
		return new Auth_OpenID_Consumer($this->getStore(), new OpenIdSession());
	}
}