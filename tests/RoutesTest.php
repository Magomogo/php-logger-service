<?php

use Mockery as m;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\Request;

class RoutesTest extends PHPUnit_Framework_TestCase
{

    public function testPostNewRecord()
    {
        $app = self::app();
        $app['couchdb.connection']->shouldReceive('postDocument')
            ->with(m::on(function($arg) {
                        $this->assertEquals(Logger::NOTICE, $arg['level']);
                        $this->assertEquals('catalog', $arg['channel']);
                        $this->assertEquals('This is a test', $arg['message']);
                        $this->assertInternalType('array', $arg['context']);
                        return true;
                    }))
            ->once();

        $response = $app->handle(
            Request::create(
                '/catalog/notice',
                'POST',
                array_merge(
                    array('message' => 'This is a test'),
                    debug_backtrace()
                )
            )
        );

        $this->assertSame(201, $response->getStatusCode());
    }

    /**
     * @dataProvider routes
     */
    public function testLogLevels($uri, $expectedStatus)
    {
        $this->assertSame($expectedStatus, self::app()->handle(Request::create($uri, 'POST'))->getStatusCode());
    }

    public static function routes()
    {
        return [
            ['/catalog/debug', 201],
            ['/catalog/info', 201],
            ['/catalog/notice', 201],
            ['/catalog/warning', 201],
            ['/catalog/error', 201],
            ['/catalog/critical', 201],
            ['/catalog/alert', 201],
            ['/catalog/emergency', 201],
            ['/catalog/notexisting', 404],
            ['/catalog/notdebug', 404],
        ];
    }

    /**
     * @return Application
     */
    private static function app()
    {
        $app = new Application();
        $app['debug'] = true;
        $app['couchdb.connection'] = m::mock(
            'Doctrine\\CouchDB\\CouchDBClient',
            function($mock) {$mock->shouldIgnoreMissing();}
        );
        return $app;
    }

}
