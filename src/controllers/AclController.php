<?php

namespace Ashamnx\Acl;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;

class AclController extends Controller {
	const PER_PAGE = 10;
	
	protected $token;
	protected $client;
	protected $user;

	public function __construct()
	{
		$this->token = Input::get('token');
		// $authSession = \Oauth::whereRaw('token = ? and status = ?',[\Input::get('token'),'active'])->get()->first();
		// $this->client = \Client::find($authSession->client_id);
		 $this->user = Config::get('auth.providers.users.model');
	}



}
