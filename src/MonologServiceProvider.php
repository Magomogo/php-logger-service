<?php

use Monolog\Handler\DoctrineCouchDBHandler;
use Silex\Application;
use Monolog\Logger;
use Silex\ServiceProviderInterface;

class MonologServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['logger.factory'] = $app->protect(function ($channel) use ($app) {
                $log = new Logger($channel);

                $log->pushHandler(new DoctrineCouchDBHandler($app['couchdb.connection']));

                return $log;
            });
    }

    public function boot(Application $app)
    {
    }
}