<?php

namespace JiraRestApi\Tests;

use GuzzleHttp\Psr7\Response;
use JiraRestApi\JiraClientResponse;
use JiraRestApi\Project\Project;
use JiraRestApi\Project\ProjectService;

class ProjectTest extends MockGuzzleClient
{
    public function testGetAllProjects()
    {
        $response = $this->getLocalResponse('projects.get.json');
        $projectService = $this->app['jira.rest.project'];
        $this->mockHandler->append(new Response(200, [], $response));

        /** @var ProjectService $projectService */
        $result = $projectService->getAllProjects();

        $this->assertInstanceOf(\ArrayObject::class, $result);
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(Project::class, $result->offsetGet(0));
    }

    public function testGetProject()
    {
        $response = $this->getLocalResponse('project.get.json');
        $projectService = $this->app['jira.rest.project'];
        $this->mockHandler->append(new Response(200, [], $response));

        /** @var ProjectService $projectService */
        $result = $projectService->get('EX');

        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(Project::class, $result);
    }

    public function testGetProjectNotFound()
    {
        $response = $this->getLocalResponse('error.json');
        $projectService = $this->app['jira.rest.project'];
        $this->mockHandler->append(new Response(404, [], $response));

        /** @var JiraClientResponse $result */
        $result = $projectService->get('ERP');

        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(JiraClientResponse::class, $result);

        $this->assertArrayHasKey('errorMessages', $result->getError());
//        $this->assertEquals(json_decode($response, true), $result->getError());
    }
}