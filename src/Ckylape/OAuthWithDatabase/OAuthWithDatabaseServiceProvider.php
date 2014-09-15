<?php namespace Ckylape\OAuthWithDatabase;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class OAuthWithDatabaseServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('ckylape/oauth-with-db');

        Auth::extend('oauth-with-db', function($app) {
            $provider =  new OAuthWithDatabaseUserProvider();
            return new OAuthWithDatabaseGuard($provider, $app['session.store']);
        });
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}