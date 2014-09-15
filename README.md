OAuth with Database
====================
Laravel Custom Auth Driver that uses OAuth 2.0 for Authorization and MySQL for Authentication.


## Setup and Configuration

To use the package, set the ```app/config/auth.php``` driver to ```oauth-with-db```. 

The package uses a configuration file to pull the OAuth server settings. Create a ```app/config/oauth-with.db.php``` config file with the following array options:

```php

return array(
  	"app_id" => "APP-ID-HASH", //application identifier, normally supplied by the OAUTH server

  	"auth_host"  => "http://auth.example.com/api/1.0/server", //url in which cURL requests are sent/received

  	"login_url"  => "http://auth.example.com/login", //url in which the user will submit their credentials

	"public_key"  => "" //this could be a RSA key or something more simple
);
```


## Using the Auth Driver

The package extends ```Illuminate\Auth\Guard``` but the use of ```Auth``` remains mostly the same. Below you can find an exmample of setting up a Route filter:

```php
Route::filter('auth', function()
{	
	// Check if User is submitting via AJAX
	if (Request::ajax())
	{
		return Response::make('Unauthorized', 401);
	}

	// Check if the User is logged in
	if (Auth::check())
	{
		if(Input::has('AuthToken')) 
		{
			return Redirect::to(Request::url());
		}
	} else {
		return Auth::redirect();
	}
});
```


## TO DO 

* Finish the custom Guard Provider by adding the rest of the ```Auth``` public functions so the behavior is almost identical to the [```Illuminate\Auth\Guard```](https://github.com/illuminate/auth/blob/master/Guard.php)
* Create the custom User Provider and User Model similar to [```Illuminate\Auth\EloquentUserProvider```](https://github.com/illuminate/auth/blob/master/EloquentUserProvider.php)