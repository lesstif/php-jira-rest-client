<?php

namespace JiraRestApi\ServiceDesk;

use JiraRestApi\JiraClient;
use JsonMapper_Exception;
use Psr\Log\LoggerInterface;

class ServiceDeskClient extends JiraClient
{
    /**
     * Get URL by context.
     *
     * @param string $context
     *
     * @return string
     */
    protected function createUrlByContext($context)
    {
        $host = $this->getConfiguration()->getJiraHost();

        return sprintf('%s/rest/servicedeskapi/%s', $host, preg_replace('/\//', '', $context, 1));
    }

    public function getLogger(): LoggerInterface
    {
        return $this->log;
    }

    public function log(string $message): void
    {
        $this->log->info($message);
    }

    public function createUrl(string $format, array $parameters, array $urlParameters = []): string
    {
        if (count($urlParameters) > 0)
        {
            $format .= '?%s';
            $parameters[] = http_build_query($urlParameters);
        }

        array_unshift($parameters, $format);

        return call_user_func_array("sprintf", $parameters);
    }

    /**
     * @param mixed $dataObject
     * @return mixed
     * @throws JsonMapper_Exception
     */
    public function map(string $jsonData, $dataObject)
    {
        $data = json_decode($jsonData, false);

        return $this->mapWithoutDecode($data, $dataObject);
    }

    /**
     * @param mixed $dataObject
     * @return mixed
     * @throws JsonMapper_Exception
     */
    public function mapWithoutDecode(object $jsonData, $dataObject)
    {
        return $this->json_mapper->map(
            $jsonData,
            $dataObject
        );
    }

    public function getServiceDeskId(): int
    {
        return $this->configuration->getServiceDeskId();
    }
}