<?php

namespace JiraRestApi\Tests;

use GuzzleHttp\Psr7\Response;
use JiraRestApi\JiraClientResponse;
use JiraRestApi\Webhook\Webhook;
use JiraRestApi\Webhook\WebhookService;

class WebhookTest extends MockGuzzleClient
{
    public function testGetAllWebhooks()
    {
        $response = $this->getLocalResponse('webhook.getall.json');
        $webhookService = $this->app['jira.rest.webhook'];
        $this->mockHandler->append(new Response(200, [], $response));

        /** @var WebhookService $webhookService */
        $result = $webhookService->getAllWebhooks();

        $this->assertInstanceOf(\ArrayObject::class, $result);
        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(Webhook::class, $result->offsetGet(0));
    }

    public function testGetWebhook()
    {
        $response = $this->getLocalResponse('webhook.get.json');
        $webhookService = $this->app['jira.rest.webhook'];
        $this->mockHandler->append(new Response(200, [], $response));

        /** @var WebhookService $webhookService */
        $result = $webhookService->get(1);

        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(Webhook::class, $result);
    }

    public function testCreateWebhook()
    {
        $response = $this->getLocalResponse('webhook.create.json');
        $webhookService = $this->app['jira.rest.webhook'];
        $this->mockHandler->append(new Response(201, [], $response));

        $newHook = new Webhook();
        $newHook
            ->setName('test webhook')
            ->setUrl('http://192.168.33.10/webhook')
            ->setEvents([
                Webhook::JIRA_ISSUE_CREATED,
                Webhook::JIRA_ISSUE_UPDATED,
                Webhook::JIRA_ISSUE_DELETED,
                Webhook::JIRA_WORKLOG_UPDATED
            ])
            ->setExcludeIssueDetails(false);

        /** @var WebhookService $webhookService */
        $result = $webhookService->create($newHook);

        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(Webhook::class, $result);
    }

    public function testDeleteWebhook()
    {
        $webhookService = $this->app['jira.rest.webhook'];
        $this->mockHandler->append(new Response(204, []));

        /** @var WebhookService $webhookService */
        $result = $webhookService->delete(1);

        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(JiraClientResponse::class, $result);
    }
}