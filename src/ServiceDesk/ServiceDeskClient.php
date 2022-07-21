<?php

declare(strict_types=1);

namespace JiraRestApi\ServiceDesk;

use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\JiraClient;
use JsonMapper;
use Psr\Log\LoggerInterface;

class ServiceDeskClient extends JiraClient
{
    public function __construct(
        ConfigurationInterface $configuration = null,
        LoggerInterface $logger = null,
        string $path = './'
    ) {
        parent::__construct($configuration, $logger, $path);

        $this->json_mapper->bEnforceMapType = false;
    }

    /**
     * @inheritDoc
     */
    protected function createUrlByContext(string $context): string
    {
        $host = $this->getConfiguration()->getJiraHost();

        return sprintf('%s/rest/servicedeskapi/%s', $host, preg_replace('/\//', '', $context, 1));
    }

    public function getLogger(): LoggerInterface
    {
        return $this->log;
    }

    public function getMapper(): JsonMapper
    {
        return $this->json_mapper;
    }

    public function createUrl(string $format, array $parameters, array $urlParameters = []): string
    {
        if (count($urlParameters) > 0) {
            $format .= '?%s';
            $parameters[] = http_build_query($urlParameters);
        }

        array_unshift($parameters, $format);

        return call_user_func_array('sprintf', $parameters);
    }
}
