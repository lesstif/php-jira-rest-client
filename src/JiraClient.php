<?php

namespace JiraRestApi;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Uri;
use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\Configuration\DotEnvConfiguration;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;

/**
 * Interact jira server with REST API.
 */
class JiraClient
{
    /**
     * @var string
     */
    public $cookieFile;

    /**
     * Json Mapper.
     *
     * @var \JsonMapper
     */
    protected $json_mapper;

    /**
     * HTTP response code.
     *
     * @var string|int
     */
    protected $http_response;

    /**
     * JIRA REST API URI.
     *
     * @var string
     */
    private $api_uri = '/rest/api/2';

    /**
     * CURL instance.
     *
     * @var resource
     */
    protected $curl;

    /**
     * Monolog instance.
     *
     * @var \Monolog\Logger
     */
    protected $log;

    /**
     * json en/decode options.
     *
     * @var int
     */
    protected $jsonOptions;

    /**
     * @var ClientInterface
     */
    private $client;
    /**
     * @var bool
     */
    private $useRestApiV3 = false;

    /**
     * Constructor.
     *
     * @param ConfigurationInterface|ClientInterface|null $configuration
     * @param LoggerInterface|null $logger
     * @param string $path
     *
     * @param bool $useJiraV3
     * @param bool $jiraLogEnabled
     * @throws JiraException
     */
    public function __construct($configuration = null, LoggerInterface $logger = null, $path = './', bool $useJiraV3 = false, bool $jiraLogEnabled = true)
    {
        if (!$configuration) {
            $configuration = new DotEnvConfiguration();
        }

        if ($useJiraV3 || ($configuration instanceof ConfigurationInterface && $configuration->getUseV3RestApi())) {
            $this->setRestApiV3();
        }

        $this->configureClient($configuration, $path);

        $this->json_mapper = new \JsonMapper();

        // Fix "\JiraRestApi\JsonMapperHelper::class" syntax error, unexpected 'class' (T_CLASS), expecting identifier (T_STRING) or variable (T_VARIABLE) or '{' or '$'
        $this->json_mapper->undefinedPropertyHandler = [new \JiraRestApi\JsonMapperHelper(), 'setUndefinedProperty'];

        // Properties that are annotated with `@var \DateTimeInterface` should result in \DateTime objects being created.
        $this->json_mapper->classMap['\\'.\DateTimeInterface::class] = \DateTime::class;

        // create logger
        if ($jiraLogEnabled || ($configuration instanceof ConfigurationInterface && $configuration->getJiraLogEnabled())) {
            if ($logger) {
                $this->log = $logger;
            } else {
                $this->log = new Logger('JiraClient');
                $this->log->pushHandler(new StreamHandler(
                    $configuration->getJiraLogFile(),
                    $this->convertLogLevel($configuration->getJiraLogLevel())
                ));
            }
        } else {
            $this->log = new Logger('JiraClient');
            $this->log->pushHandler(new NoOperationMonologHandler());
        }

        $this->http_response = 200;

        $this->jsonOptions = JSON_UNESCAPED_UNICODE;

        if (PHP_MAJOR_VERSION >= 7) {
            if (PHP_MAJOR_VERSION === 7 && PHP_MINOR_VERSION >= 3) {
                $this->jsonOptions |= JSON_THROW_ON_ERROR;
            } elseif (PHP_MAJOR_VERSION >= 8) { // if php major great than 7 then always setting JSON_THROW_ON_ERROR
                $this->jsonOptions |= JSON_THROW_ON_ERROR;
            }
        }
    }

    /**
     * @param ConfigurationInterface|ClientInterface|null $configuration
     * @param string $path
     * @throws JiraException
     */
    private function configureClient($configuration = null, string $path = "./"): void
    {
        if ($configuration instanceof ClientInterface) {
            $this->client = $configuration;
            return;
        }

        if ($configuration === null) {
            if (!file_exists($path.'.env')) {
                // If calling the getcwd() on laravel it will returning the 'public' directory.
                $path = '../';
            }
            $configuration = new DotEnvConfiguration($path);
        }

        $config = [
            'handler' => HandlerStack::create(),
            'base_uri' => $configuration->getJiraHost(),
            'verify' => $configuration->isCurlOptSslVerifyHost(),
            'http_errors' => false,
            'headers' => [
                'User-Agent' => $configuration->getCurlOptUserAgent(),
                'Accept' => '*/*',
                'Content-Type' => 'application/json',
                'X-Atlassian-Token' => 'no-check'
            ]
        ];

        if ($this->useRestApiV3) {
            $config['headers']['x-atlassian-force-account-id'] = true;
        }

        if ($configuration->getProxyServer()) {
            $uri = (new Uri($configuration->getProxyServer()))
                ->withPort($configuration->getProxyPort());

            if ($configuration->getProxyUser()) {
                $uri = $uri->withUserInfo($configuration->getProxyUser(), $configuration->getProxyPassword());
            }

            $config['proxy'] = (string) $uri;
        }

        if ($configuration->isCurlOptSslCert()) {
            $config['cert'] = $configuration->isCurlOptSslCert();

            if ($configuration->isCurlOptSslCertPassword()) {
                $config['cert'] = [$configuration->isCurlOptSslCert(), $configuration->isCurlOptSslCertPassword()];
            }
        }

        if ($configuration->isCurlOptSslKey()) {
            $config['ssl_key'] = $configuration->isCurlOptSslKey();

            if ($configuration->isCurlOptSslCertPassword()) {
                $config['ssl_key'] = [$configuration->isCurlOptSslKey(), $configuration->isCurlOptSslKeyPassword()];
            }
        }


        if ($configuration->isCookieAuthorizationEnabled()) {
            $jar = new FileCookieJar($configuration->getCookieFile());
            $config['cookies'] = $jar;

            $this->log->debug('Using cookie..');
        }

        if (!file_exists($configuration->getCookieFile())) {
            $config['auth'] = [$configuration->getJiraUser(), $configuration->getJiraPassword()];
        }

        $this->client = new GuzzleAdapter(new Client($config));
    }

    /**
     * Convert log level.
     *
     * @param string $log_level
     *
     * @return int
     */
    private function convertLogLevel(string $log_level)
    {
        $log_level = strtoupper($log_level);

        switch ($log_level) {
            case 'EMERGENCY':
                return Logger::EMERGENCY;
            case 'ALERT':
                return Logger::ALERT;
            case 'CRITICAL':
                return Logger::CRITICAL;
            case 'ERROR':
                return Logger::ERROR;
            case 'WARNING':
                return Logger::WARNING;
            case 'NOTICE':
                return Logger::NOTICE;
            case 'DEBUG':
                return Logger::DEBUG;
            case 'INFO':
                return Logger::INFO;
            default:
                return Logger::WARNING;
        }
    }

    /**
     * Serilize only not null field.
     *
     * @param array $haystack
     *
     * @return array
     */
    protected function filterNullVariable($haystack)
    {
        foreach ($haystack as $key => $value) {
            if (is_array($value)) {
                $haystack[$key] = $this->filterNullVariable($haystack[$key]);
            } elseif (is_object($value)) {
                $haystack[$key] = $this->filterNullVariable(get_class_vars(get_class($value)));
            }

            if (is_null($haystack[$key]) || empty($haystack[$key])) {
                unset($haystack[$key]);
            }
        }

        return $haystack;
    }

    /**
     * Execute REST request.
     *
     * @param string $context Rest API context (ex.:issue, search, etc..)
     * @param null $post_data
     * @param null $custom_request [PUT|DELETE]
     * @return string|bool
     * @throws JiraException
     */
    public function exec($context, $post_data = null, $custom_request = null)
    {
        $url = $this->createUrlByContext($context);

        if (is_string($post_data)) {
            $this->log->info("Curl $custom_request: $url JsonData=".$post_data);
        } elseif (is_array($post_data)) {
            $this->log->info("Curl $custom_request: $url JsonData=".json_encode($post_data, JSON_UNESCAPED_UNICODE));
        }

        $method = $custom_request ?? "GET";
        $body = $method !== "GET" ? $post_data : null;

        $response = $this->client->sendRequest(new Request($method, $url, [], $body));
        $this->http_response = $response->getStatusCode();
        $content = $response->getBody()->getContents();

        if (!$content) {
            if (in_array($this->http_response, [204,201,200], true)) {
                return true;
            }

            // HostNotFound, No route to Host, etc Network error
            $msg = sprintf('CURL Error: http response=%d, %s', $this->http_response, $content);
            $this->log->error($msg);
            throw new JiraException($msg);
        }

        if (!in_array($this->http_response, [201,200], true)) {
            throw new JiraException('CURL HTTP Request Failed: Status Code : '
                .$this->http_response.', URL:'.$url
                ."\nError Message : ".$content, $this->http_response);
        }

        return $content;
    }

    /**
     * File upload.
     *
     * @param string $context       url context
     * @param array  $filePathArray upload file path.
     *
     * @throws JiraException
     *
     * @return array
     */
    public function upload($context, $filePathArray)
    {
        $url = $this->createUrlByContext($context);

        $idx = 0;
        $results = [];
        foreach ($filePathArray as $file) {
            $request = new Request("POST", $url, ['Content-Type' => 'multipart/form-data'], new Stream(fopen($file, "r+b")));
            $response = $this->client->sendRequest($request);
            $this->http_response = $response->getStatusCode();
            $contents = $response->getBody()->getContents();

            if (in_array($this->http_response, [204,201,202], true)) {
                $results[$idx] = $contents;
            } else {
                $msg = sprintf('CURL Error: http response=%d, %s', $this->http_response, $contents);
                $this->log->error($msg);

                throw new JiraException($msg);
            }
            $idx++;
        }

        return $results;
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
        return $this->api_uri.'/'.preg_replace('/\//', '', $context, 1);
    }

    /**
     * Set a custom Jira API URI for the request.
     *
     * @param string $api_uri
     */
    public function setAPIUri($api_uri)
    {
        $this->api_uri = $api_uri;
    }

    /**
     * convert to query array to http query parameter.
     *
     * @param array $paramArray
     *
     * @return string
     */
    public function toHttpQueryParameter(array $paramArray)
    {
        $queryParam = '?';

        foreach ($paramArray as $key => $value) {
            $v = null;

            // some param field(Ex: expand) type is array.
            if (is_array($value)) {
                $v = implode(',', $value);
            } else {
                $v = $value;
            }

            $queryParam .= $key.'='.$v.'&';
        }

        return $queryParam;
    }

    /**
     * download and save into outDir.
     *
     * @param string $url        full url
     * @param string $outDir     save dir
     * @param string $file       save filename
     * @param string $cookieFile cookie filename
     *
     * @throws JiraException
     *
     * @return bool|mixed
     */
    public function download(string $url, string $outDir, string $file)
    {
        $outputFile = fopen($outDir.DIRECTORY_SEPARATOR.$file, 'w+b');

        $response = $this->client->sendRequest(new Request("GET", $url));
        $this->http_response = $response->getStatusCode();

        $stream = $response->getBody();

        if (!in_array($this->http_response, [200,201], true)) {
            $msg = sprintf('CURL Error: http response=%d, %s', $this->http_response, $stream->getContents());
            $this->log->error($msg);
            throw new JiraException($msg);
        }

        while (!$stream->eof()) {
            fwrite($outputFile, $stream->read(4096));
        }
        fclose($outputFile);

        return $response;
    }

    /**
     * setting cookie file path.
     *
     * @param string $cookieFile
     *
     * @return $this
     */
    public function setCookieFile(string $cookieFile)
    {
        $this->cookieFile = $cookieFile;

        return $this;
    }

    /**
     * setting REST API url to V3.
     *
     * @return $this
     */
    public function setRestApiV3()
    {
        $this->api_uri = '/rest/api/3';
        $this->useRestApiV3 = true;

        return $this;
    }

    /**
     * check whether current API is v3.
     *
     * @return bool
     */
    public function isRestApiV3()
    {
        return $this->useRestApiV3;
    }

    /**
     * setting JSON en/decoding options.
     *
     * @param int $jsonOptions
     *
     * @return $this
     */
    public function setJsonOptions(int $jsonOptions)
    {
        $this->jsonOptions = $jsonOptions;

        return $this;
    }

    /**
     * get json en/decode options.
     *
     * @return int
     */
    public function getJsonOptions()
    {
        return $this->jsonOptions;
    }
}
