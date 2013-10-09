<?php

use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;

class Application extends Silex\Application
{
    public function __construct()
    {
        parent::__construct();

        $this->register(
            new CouchDbServiceProvider(),
            array(
                'couchdb.params' => [
                    'dbname' => 'log'
                ]
            )
        );

        $this->register(new MonologServiceProvider());

        $this->post(
            '{channel}/{level}',
            function ($channel, $level) {

                /** @var Logger $logger */
                $logger = $this['logger.factory']($channel);

                $logger->log(
                    $level,
                    $this->request()->request->get('message'),
                    array_diff_key($this->request()->request->all(), array('message' => null))
                );

                return $this->json(['Ok'], 201);
            }
        )->assert('level', '(debug|info|notice|warning|error|critical|alert|emergency)');
    }

    /**
     * @return Request
     */
    public function request()
    {
        return $this['request'];
    }
}