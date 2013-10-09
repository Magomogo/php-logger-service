<?php


use Doctrine\CouchDB\CouchDBClient;
use Silex\Application;
use Silex\ServiceProviderInterface;

class CouchDbServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['couchdb.connection'] = $app->share(function () use ($app) {
                return CouchDBClient::create($app['couchdb.params']);
            });
    }

    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
    }
}