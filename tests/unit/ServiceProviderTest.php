<?php

namespace JiraRestApi\Tests;

use JiraRestApi\JiraClient;
use JiraRestApi\Project\ProjectService;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;


class ServiceProviderTest extends MockGuzzleClient
{
    public function testRestServiceRegistered()
    {
        $this->app->get('/', function () {
            return 'ok';
        });

        $request = Request::create('/');
        $response = $this->app->handle($request);

        $this->assertEquals('ok', $response->getContent());
        $this->assertInstanceOf(ProjectService::class, $this->app['jira.rest.project']);
        $this->assertInstanceOf(JiraClient::class, $this->app['jira.rest.project']);
    }
}