<?php

namespace JiraRestApi\Webhook;

use JiraRestApi\JiraClient;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class WebhookService
 * @package JiraRestApi\Webhook
 */
class WebhookService extends JiraClient
{
    protected $api_uri = '/rest/webhooks/1.0';
    private $uri = '/webhook';

    /**
     * get all project list.
     * @return bool|mixed
     */
    public function getAllWebhooks()
    {
        $result = $this->exec($this->uri);

        return $this->extractErrors($result, [200], function () use ($result) {
            return $this->json_mapper->mapArray(
                $result->getRawData(), new \ArrayObject(), '\JiraRestApi\Webhook\Webhook'
            );
        });
    }

    /**
     * @param $webhookId
     *
     * @return bool|object
     * @throws \JsonMapper_Exception
     */
    public function get($webhookId)
    {
        $result = $this->exec($this->uri . '/' . $webhookId);

        return $this->extractErrors($result, [200], function () use ($result) {
            return $this->json_mapper->map(
                $result->getRawData(), new Webhook()
            );
        });
    }

    /**
     * @param Webhook $webhook
     *
     * @return mixed
     */
    public function create(Webhook $webhook)
    {
        $data = $this->filterNullVariable($webhook);

        $result = $this->exec($this->uri, $data, Request::METHOD_POST);

        return $this->extractErrors($result, [201], function () use ($result) {
            return $this->json_mapper->map(
                $result->getRawData(), new Webhook()
            );
        });
    }

    /**
     * @param $webhookId
     *
     * @return mixed
     */
    public function delete($webhookId)
    {
        $result = $this->exec($this->uri . '/' . $webhookId, null, Request::METHOD_DELETE);

        return $this->extractErrors($result, [204], function () use ($result) {
            return $result;
        });
    }
}

