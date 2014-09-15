<?php namespace Ckylape\OAuthWithDatabase;

use Illuminate\Auth\UserProviderInterface;
use Illuminate\Auth\GenericUser;

class OAuthWithDatabaseUserProvider implements UserProviderInterface {
  public $appID;
  public $publicKey;
  public $response;
  public $authURL;
 	
  public function retrieveByID($username) {
  	echo 'retrieveByID<br>';
    var_dump($username); exit;
  }

  public function retrieveByCredentials(array $credentials) {
     echo 'retrieveByCredentials'; exit;
  }

  public function validateCredentials(\Illuminate\Auth\UserInterface $user, array $credentials) {
  		echo 'validateCredentials'; exit;
  }

  public function retrieveByToken($identifier, $token) {
  		echo 'retrieveByToken'; exit; 
  }

  public function updateRememberToken(\Illuminate\Auth\UserInterface  $user, $token) {
  		echo 'updateRememberToken'; exit; 
  }
   
}