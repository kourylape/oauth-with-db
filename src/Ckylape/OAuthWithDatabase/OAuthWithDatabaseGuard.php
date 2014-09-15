<?php namespace Ckylape\OAuthWithDatabase;

use Illuminate\Auth\Guard;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Input;
use Illuminate\Auth\UserProviderInterface;
use Config;

class OAuthWithDatabaseGuard extends Guard {
	
	protected $publicKey;
	protected $appID;
	protected $hostURL;
	protected $loginURL;
	protected $loggedOut = false;
	protected $user;
	protected $provider;

	/**
	 * Create custom authentication guard.
	 *
	 * @return void
	 */
	public function __construct(UserProviderInterface $provider) 
	{
		$this->appID = Config::get('oauth-with-db.app_id');
		$this->hostURL = Config::get('oauth-with-db.auth_host');
		$this->loginURL = Config::get('oauth-with-db.login_url');
		$this->publicKey = Config::get('oauth-with-db.public_key');
		$this->provider = $provider;
	}

	/**
	 * Determine if the current user is authenticated.
	 *
	 * @return bool
	 */
	public function check()
	{	
		if(Session::has('AuthToken')) {
			return true;
		}  else {
			if(Input::has('AuthToken')) {
				$this->getUserDetails();
			} else {
				return false;
			}
		}
	}

	/**
	 * Determine if the current user is a guest.
	 *
	 * @return bool
	 */
	public function guest()
	{
		return ! $this->check();
	}


	/**
	 * Redirect user to login using OAuth credentials.
	 *
	 * @return Illuminate\Support\Facades\Redirect $forward_url
	 */
	public function redirect()
	{
		$comToken = $this->getNewComToken();
		Session::put('ComToken',$comToken);
		$callback = Request::url();
		$forward_url = $this->loginURL ."?ComToken=". $comToken ."&callback_url=". urlencode($callback);
		return Redirect::to($forward_url);
	}

	/**
	 * Generate API Communication Token via cURL 
	 *
	 * @return bool|string $reponse->ComToken
	 */
	private function getNewComToken()
    {	
    	$post_data = array();
        $post_data['AppID']       	  = $this->appID;
        $post_data['PublicKey']   	  = $this->publicKey;
        $response = $this->curlRequest($post_data);   
        if($response) {
	      	if($response->status_code === 200) {
	       		return $response->ComToken;
	       	} else {
	       		return false;
	       	}
	    } else {
	    	return false;
	    }   
    }

    private function getUserDetails()
    {
        $post_data = array();
        $post_data['ComToken']     = Session::get('ComToken');
        $post_data['AuthToken']    = Input::get('AuthToken');
        $post_data['UserDetails']  = true;    
        $response = $this->curlRequest($post_data);     
        if($response) {
	      	if($response->status_code === 200) {
	      		$user_details = json_decode($response->user_details);
	       		Session::put('username',$user_details->username);
	       		Session::put('AuthToken',Input::get('AuthToken'));
	       		return true;
	       	} else {
	       		
	       		return false;
	       	}
	    } else {
	    	return false;
	    }
    }

    private function curlRequest($post_data)
    {
    	$post_data['REMOTE_ADDR']     = $_SERVER['REMOTE_ADDR'];
        $post_data['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->hostURL,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => array('request' => json_encode($post_data) )
        ));
        $resp = curl_exec($curl);
        if(!$resp) {
			curl_close($curl);
            return false;
        }
        curl_close($curl);
		return json_decode($resp);
    }


    public function logout()
    {	
        $post_data = array();
        $post_data['ComToken']        = Session::pull('ComToken');
        $post_data['Logout']    	  = Session::pull('AuthToken');
        $response = $this->curlRequest($post_data);     
        Session::flush();
	    $this->loggedOut = true;
	}


	public function user()
	{
		if($this->loggedOut) return;

		if( ! is_null($this->user))
		{
			return $this->user;
		}

		$user = null;

		if( ! is_null($this->id())) 
		{
			$user = $this->provider->retrieveById($this->id());
		}

		return $this->user = $user;
	}

	public function id()
	{
		if($this->loggedOut) return;

		return Session::get('username');
	}

	//TODO:
	//1 - public function once :: login without sessions or cookies (useless)
	//2 - public function validate :: validate user's credentials
	//3 - public funciton basic :: using http basic auth (useless)
	//4 - public function onecbasic :: basic auth without sessions or cookies(useless)
	//5 - public function attempt :: attempt to authenticate user with credenitals (useless)
	//6 - public function attempting :: registers even listener
	//7 - public function login :: log user into application
	//8 - public function loginusingid :: log the given user id into the application
	//9 - public function onceusingid :: (useless)
	
}