<?php

namespace JiraRestApi;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use JiraRestApi\Interfaces\ConfigurationInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interact jira server with REST API.
 */
class JiraClient
{
    /**
     * Json Mapper.
     *
     * @var \JsonMapper
     */
    protected $json_mapper;

    /**
     * JIRA REST API URI.
     *
     * @var string
     */
    protected $api_uri = '/rest/api/2';

    /**
     * Logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $log;

    /**
     * @var ClientInterface string
     */
    protected $transport;

    /**
     * Jira Rest API Configuration.
     *
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * JiraClient constructor.
     *
     * @param ConfigurationInterface|null $configuration
     * @param ClientInterface             $transport
     * @param LoggerInterface             $log
     */
    public function __construct(ConfigurationInterface $configuration = null, ClientInterface $transport, LoggerInterface $log)
    {
        $this->configuration = $configuration;

        $this->json_mapper = new \JsonMapper();
        $this->json_mapper->bEnforceMapType = false;
        $this->json_mapper->setLogger($log);
        $this->json_mapper->undefinedPropertyHandler = function ($obj, $val) {
            $this->log->info('Handle undefined property', [$val, $obj]);
        };

        $this->log = $log;
        $this->transport = $transport;
    }

    /**
     * Execute REST request.
     *
     * @param string $context RestAPI context (ex.:issue, search, etc..)
     * @param null   $post_data
     * @param string $httpMethod
     *
     * @return string
     *
     * @throws JiraException
     */
    public function exec($context, $post_data = null, $httpMethod = Request::METHOD_GET)
    {
        $url = $this->createUrlByContext($context);

        $options = [
            RequestOptions::HEADERS => [
                'Accept' => '*/*',
                'Content-Type' => 'application/json',
                'charset' => 'UTF-8'
            ]
        ];

        if ($httpMethod == Request::METHOD_GET) {
            $options[RequestOptions::QUERY] = $post_data;
        }

        if (in_array($httpMethod, [Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_DELETE])) {
            $options[RequestOptions::JSON] = $post_data;
        }

        try {
            $this->log->info('JiraRestApi request: ', [$httpMethod, $url, $options]);
            $response = $this->transport->request($httpMethod, $url, $options);
            $this->log->info('JiraRestApi response: ', [$response->getHeaders(), (string) $response->getBody()]);
        } catch (RequestException $e) {
            $this->log->error('JiraRestApi response fail with code : ' . $e->getCode(), []);
            $response = $e->getResponse();
        }

        return $this->parseResponse($response);
    }

    /**
     * File upload.
     *
     * @param string $context       url context
     * @param array  $filePathArray upload file path.
     *
     * @return array
     *
     * @throws JiraException
     */
    public function upload($context, array $filePathArray)
    {
        $url = $this->createUrlByContext($context);

        $options = [
            RequestOptions::HEADERS => [
                'Accept' => '*/*',
                'Content-Type' => 'multipart/form-data',
                'X-Atlassian-Token' => 'nocheck'
            ]
        ];

        $promises = [];

        foreach ($filePathArray as $filePath) {
            // load each files separately
            if (file_exists($filePath) == false) {
                // Ignore if file not found
                $this->log->error('JiraRestApi: Unable to upload file "' . $filePath . '". File not Found');
                continue;
            }

            $options[RequestOptions::SINK] = $filePath;

            $this->log->info('JiraRestApi requestAsync: ', [Request::METHOD_POST, $url, $options]);
            $promises[] = $this->transport
                ->requestAsync(Request::METHOD_POST, $url, $options)
                ->then(function (ResponseInterface $response) {
                    $this->log->info('JiraRestApi responseAsync: ', [$response->getHeaders(), (string) $response->getBody()]);
                    return $response;
                }, function (RequestException $e) {
                    $this->log->error('JiraRestApi responseAsync fail with code : ' . $e->getCode(), []);
                    return $e->getResponse();
                });
        }

        $responses = \GuzzleHttp\Promise\settle($promises)->wait();

        $result = [];
        foreach ($responses as $response) {
            if (isset($response['value']) && $response['value'] instanceof ResponseInterface) {
                $result[] = $this->parseResponse($response['value']);
            }
        }

        return $result;
    }

    /**
     * @param               $array
     * @param callable|null $callback
     *
     * @return mixed
     */
    protected function filterNullVariable($array, callable $callback = null)
    {
        $array = json_decode(json_encode($array), true); // toArray

        $array = is_callable($callback) ? array_filter($array, $callback) : array_filter((array)$array);
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = call_user_func([$this, 'filterNullVariable'], $value, $callback);
            }
        }

        return $array;
    }

    /**
     * @param $rawResponse
     *
     * @return mixed
     */
    public function parseResponse(ResponseInterface $rawResponse)
    {
        return (new JiraClientResponse($rawResponse, $this->log))->parse();
    }

    /**
     * @param          $result
     * @param array    $responseCodes
     * @param \Closure $callback
     *
     * @return mixed
     */
    protected function extractErrors($result, array $responseCodes = [200], \Closure $callback)
    {
        if ($result instanceof JiraClientResponse &&
            !$result->hasErrors() &&
            in_array($result->getCode(), $responseCodes)
        ) {
            return $callback();
        }

        if (!in_array($result->getCode(), $responseCodes)) {
            $result->setError('Unexpected response code, expected "' . implode(', ', $responseCodes) . '", ' . $result->getCode() . ' given');
        }

        return $result;
    }

    /**
     * Get URL by context.
     *
     * @param string $context
     *
     * @return string
     */
    protected function createUrlByContext($context)
    {
        return $this->api_uri . '/' . preg_replace('/\//', '', $context, 1);
    }

    /**
     * Jira Rest API Configuration.
     *
     * @return ConfigurationInterface
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
