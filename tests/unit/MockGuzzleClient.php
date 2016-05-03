<?php

namespace JiraRestApi\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use JiraRestApi\JiraRestApiSilexServiceProvider;
use Monolog\Logger;
use Silex\Application;
use Silex\Provider\MonologServiceProvider;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;


class MockGuzzleClient extends \PHPUnit_Framework_TestCase
{
    /**
     * @var
     */
    protected $app;

    /** @var MockHandler */
    protected $mockHandler;

    protected function setUp()
    {
        $app = new Application();

        ErrorHandler::register();
        ExceptionHandler::register(true);

        $app->register(new MonologServiceProvider(), [
            'monolog.name' => 'APP',
            'monolog.logfile' => __DIR__ . '/../../runtime/logs/error.log',
            'monolog.level' => Logger::INFO
        ]);

        $app->register(new JiraRestApiSilexServiceProvider(), ['jira.config' => [
            'jiraHost' => 'https://jira.atlassian.com',
            'jiraUsername' => 'anonymous',
            'jiraPassword' => ''
        ]]);

        $this->mockHandler = new MockHandler();
        $app['jira.rest.transport'] = $app->share(function () {
            return new Client(['handler' => HandlerStack::create($this->mockHandler)]);
        });

        $this->app = $app;
    }

    protected function tearDown()
    {
        unset($this->mockHandler);
    }

    /**
     * @param null $filename
     * @return null
     */
    protected function getLocalResponse($filename = null)
    {
        $pathToFiles = __DIR__ . '/../fixtures/';

        return file_exists($pathToFiles . $filename)
            ? file_get_contents($pathToFiles . $filename)
            : null;
    }
}