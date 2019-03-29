<?php

namespace JiraRestApi;

use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\Configuration\DotEnvConfiguration;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as Logger;
use Psr\Log\LoggerInterface;

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
     * HTTP response code.
     *
     * @var string
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
     * Jira Rest API Configuration.
     *
     * @var ConfigurationInterface
     */
    protected $configuration;

    /**
     * Constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param LoggerInterface        $logger
     * @param string                 $path
     *
     * @throws JiraException
     * @throws \Exception
     */
    public function __construct(ConfigurationInterface $configuration = null, LoggerInterface $logger = null, $path = './')
    {
        if ($configuration === null) {
            if (!file_exists($path.'.env')) {
                // If calling the getcwd() on laravel it will returning the 'public' directory.
                $path = '../';
            }
            $this->configuration = new DotEnvConfiguration($path);
        } else {
            $this->configuration = $configuration;
        }

        $this->json_mapper = new \JsonMapper();

        // Fix "\JiraRestApi\JsonMapperHelper::class" syntax error, unexpected 'class' (T_CLASS), expecting identifier (T_STRING) or variable (T_VARIABLE) or '{' or '$'
        $this->json_mapper->undefinedPropertyHandler = [new \JiraRestApi\JsonMapperHelper(), 'setUndefinedProperty'];

        // Properties that are annotated with `@var \DateTimeInterface` should result in \DateTime objects being created.
        $this->json_mapper->classMap['\\'.\DateTimeInterface::class] = \DateTime::class;

        // create logger
        if ($this->configuration->getJiraLogEnabled()) {
            if ($logger) {
                $this->log = $logger;
            } else {
                $this->log = new Logger('JiraClient');
                $this->log->pushHandler(new StreamHandler(
                    $this->configuration->getJiraLogFile(),
                    $this->convertLogLevel($this->configuration->getJiraLogLevel())
                ));
            }
        } else {
            $this->log = new Logger('JiraClient');
            $this->log->pushHandler(new NoOperationMonologHandler());
        }

        $this->http_response = 200;

        $this->curl = curl_init();
    }

    /**
     * Convert log level.
     *
     * @param $log_level
     *
     * @return int
     */
    private function convertLogLevel($log_level)
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
     * @param string $context        Rest API context (ex.:issue, search, etc..)
     * @param string $post_data
     * @param string $custom_request [PUT|DELETE]
     * @param string $cookieFile     cookie file
     *
     * @throws JiraException
     *
     * @return string
     */
    public function exec($context, $post_data = null, $custom_request = null, $cookieFile = null)
    {
        $url = $this->createUrlByContext($context);

        if (is_string($post_data)) {
            $this->log->info("Curl $custom_request: $url JsonData=".$post_data);
        } elseif (is_array($post_data)) {
            $this->log->info("Curl $custom_request: $url JsonData=".json_encode($post_data, JSON_UNESCAPED_UNICODE));
        }

        curl_reset($this->curl);
        $ch = $this->curl;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        // post_data
        if (!is_null($post_data)) {
            // PUT REQUEST
            if (!is_null($custom_request) && $custom_request == 'PUT') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            }
            if (!is_null($custom_request) && $custom_request == 'DELETE') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            } else {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            }
        } else {
            if (!is_null($custom_request) && $custom_request == 'DELETE') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            }
        }

        $this->authorization($ch, $cookieFile);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->getConfiguration()->isCurlOptSslVerifyHost());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getConfiguration()->isCurlOptSslVerifyPeer());
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getConfiguration()->getCurlOptUserAgent());

        // curl_setopt(): CURLOPT_FOLLOWLOCATION cannot be activated when an open_basedir is set
        if (!function_exists('ini_get') || !ini_get('open_basedir')) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }

        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            ['Accept: */*', 'Content-Type: application/json', 'X-Atlassian-Token: no-check']);

        curl_setopt($ch, CURLOPT_VERBOSE, $this->getConfiguration()->isCurlOptVerbose());

        // Add proxy settings to the curl.
        $this->proxyConfigCurlHandle($ch);

        $this->log->debug('Curl exec='.$url);
        $response = curl_exec($ch);

        // if request failed or have no result.
        if (!$response) {
            $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $body = curl_error($ch);

            /*
             * 201: The request has been fulfilled, resulting in the creation of a new resource.
             * 204: The server successfully processed the request, but is not returning any content.
             */
            if ($this->http_response === 204 || $this->http_response === 201 || $this->http_response === 200) {
                return true;
            }

            // HostNotFound, No route to Host, etc Network error
            $msg = sprintf('CURL Error: http response=%d, %s', $this->http_response, $body);

            $this->log->error($msg);

            throw new JiraException($msg);
        } else {
            // if request was ok, parsing http response code.
            $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // don't check 301, 302 because setting CURLOPT_FOLLOWLOCATION
            if ($this->http_response != 200 && $this->http_response != 201) {
                throw new JiraException('CURL HTTP Request Failed: Status Code : '
                    .$this->http_response.', URL:'.$url
                    ."\nError Message : ".$response, $this->http_response);
            }
        }

        return $response;
    }

    /**
     * Create upload handle.
     *
     * @param string $url         Request URL
     * @param string $upload_file Filename
     *
     * @return resource
     */
    private function createUploadHandle($url, $upload_file)
    {
        curl_reset($this->curl);
        $ch = $this->curl;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        // send file
        curl_setopt($ch, CURLOPT_POST, true);

        if (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 5) {
            $attachments = realpath($upload_file);
            $filename = basename($upload_file);

            curl_setopt($ch, CURLOPT_POSTFIELDS,
                ['file' => '@'.$attachments.';filename='.$filename]);

            $this->log->debug('using legacy file upload');
        } else {
            // CURLFile require PHP > 5.5
            $attachments = new \CURLFile(realpath($upload_file));
            $attachments->setPostFilename(basename($upload_file));

            curl_setopt($ch, CURLOPT_POSTFIELDS,
                ['file' => $attachments]);

            $this->log->debug('using CURLFile='.var_export($attachments, true));
        }

        $this->authorization($ch);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->getConfiguration()->isCurlOptSslVerifyHost());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getConfiguration()->isCurlOptSslVerifyPeer());

        $this->proxyConfigCurlHandle($ch);

        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); cannot be activated when an open_basedir is set
        if (!function_exists('ini_get') || !ini_get('open_basedir')) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: */*',
            'Content-Type: multipart/form-data',
            'X-Atlassian-Token: nocheck',
        ]);

        curl_setopt($ch, CURLOPT_VERBOSE, $this->getConfiguration()->isCurlOptVerbose());

        $this->log->debug('Curl exec='.$url);

        return $ch;
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

        // return value
        $result_code = 200;

        $chArr = [];
        $results = [];
        $mh = curl_multi_init();

        for ($idx = 0; $idx < count($filePathArray); $idx++) {
            $file = $filePathArray[$idx];
            if (file_exists($file) == false) {
                $body = "File $file not found";
                $result_code = -1;
                $this->closeCURLHandle($chArr, $mh, $body, $result_code);

                return $results;
            }
            $chArr[$idx] = $this->createUploadHandle($url, $filePathArray[$idx]);

            curl_multi_add_handle($mh, $chArr[$idx]);
        }

        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);

        // Get content and remove handles.
        $body = '';
        for ($idx = 0; $idx < count($chArr); $idx++) {
            $ch = $chArr[$idx];

            $results[$idx] = curl_multi_getcontent($ch);

            // if request failed.
            if (!$results[$idx]) {
                $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $body = curl_error($ch);

                //The server successfully processed the request, but is not returning any content.
                if ($this->http_response == 204) {
                    continue;
                }

                // HostNotFound, No route to Host, etc Network error
                $result_code = -1;
                $body = 'CURL Error: = '.$body;
                $this->log->error($body);
            } else {
                // if request was ok, parsing http response code.
                $result_code = $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                // don't check 301, 302 because setting CURLOPT_FOLLOWLOCATION
                if ($this->http_response != 200 && $this->http_response != 201) {
                    $body = 'CURL HTTP Request Failed: Status Code : '
                        .$this->http_response.', URL:'.$url;

                    $this->log->error($body);
                }
            }
        }

        $this->closeCURLHandle($chArr, $mh, $body, $result_code);

        return $results;
    }

    /**
     * @param array $chArr
     * @param $mh
     * @param $body
     * @param $result_code
     *
     * @throws \JiraRestApi\JiraException
     */
    protected function closeCURLHandle(array $chArr, $mh, $body, $result_code)
    {
        foreach ($chArr as $ch) {
            $this->log->debug('CURL Close handle..');
            curl_multi_remove_handle($mh, $ch);
        }
        $this->log->debug('CURL Multi Close handle..');
        curl_multi_close($mh);
        if ($result_code != 200) {
            // @TODO $body might have not been defined
            throw new JiraException('CURL Error: = '.$body, $result_code);
        }
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
        $host = $this->getConfiguration()->getJiraHost();

        return $host.$this->api_uri.'/'.preg_replace('/\//', '', $context, 1);
    }

    /**
     * Add authorize to curl request.
     *
     * @param resource $ch
     */
    protected function authorization($ch, $cookieFile = null)
    {
        // use cookie
        if ($this->getConfiguration()->isCookieAuthorizationEnabled()) {
            if ($cookieFile === null) {
                $cookieFile = $this->getConfiguration()->getCookieFile();
            }

            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);

            $this->log->debug('Using cookie..');
        }

        // if cookie file not exist, using id/pwd login
        if (!file_exists($cookieFile)) {
            $username = $this->getConfiguration()->getJiraUser();
            $password = $this->getConfiguration()->getJiraPassword();
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        }
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
     * @param $paramArray
     *
     * @return string
     */
    public function toHttpQueryParameter($paramArray)
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
     * @param $url full url
     * @param $outDir save dir
     * @param $file save filename
     * @param $cookieFile cookie filename
     *
     * @throws JiraException
     *
     * @return bool|mixed
     */
    public function download($url, $outDir = null, $file = null, $cookieFile = null)
    {
        $forceDownload = empty($outDir) || empty($file) ? true : false;

        curl_reset($this->curl);
        $ch = $this->curl;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        if (!$forceDownload) {
            $file = fopen($outDir.DIRECTORY_SEPARATOR.$file, 'w');
            // output to file handle
            curl_setopt($ch, CURLOPT_FILE, $file);
        }

        $this->authorization($ch, $cookieFile);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->getConfiguration()->isCurlOptSslVerifyHost());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getConfiguration()->isCurlOptSslVerifyPeer());
        $this->proxyConfigCurlHandle($ch);

        // curl_setopt(): CURLOPT_FOLLOWLOCATION cannot be activated when an open_basedir is set
        if (!function_exists('ini_get') || !ini_get('open_basedir')) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: */*',
            'Content-Type: application/json',
            'X-Atlassian-Token: no-check',
        ]);

        curl_setopt($ch, CURLOPT_VERBOSE, $this->getConfiguration()->isCurlOptVerbose());

        $this->log->debug('Curl exec='.$url);
        $response = curl_exec($ch);

        // if request failed.
        if (!$response) {
            $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $body = curl_error($ch);
            curl_close($ch);

            if (!$forceDownload) {
                fclose($file);
            }

            /*
             * 201: The request has been fulfilled, resulting in the creation of a new resource.
             * 204: The server successfully processed the request, but is not returning any content.
             */
            if ($this->http_response === 204 || $this->http_response === 201) {
                return true;
            }

            // HostNotFound, No route to Host, etc Network error
            $msg = sprintf('CURL Error: http response=%d, %s', $this->http_response, $body);

            $this->log->error($msg);

            throw new JiraException($msg);
        } else {
            // if request was ok, parsing http response code.
            $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($forceDownload) {
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename='.basename($url));
                header('Content-Transfer-Encoding: binary');
            } else {
                fclose($file);
            }

            curl_close($ch);

            // don't check 301, 302 because setting CURLOPT_FOLLOWLOCATION
            if ($this->http_response != 200 && $this->http_response != 201) {
                throw new JiraException('CURL HTTP Request Failed: Status Code : '
                    .$this->http_response.', URL:'.$url
                    ."\nError Message : ".$response, $this->http_response);
            }
        }

        return $response;
    }

    /**
     * setting cookie file path.
     *
     * @param $cookieFile
     *
     * @return $this
     */
    public function setCookieFile($cookieFile)
    {
        $this->cookieFile = $cookieFile;

        return $this;
    }

    /**
     * Config a curl handle with proxy configuration (if set) from ConfigurationInterface.
     *
     * @param $ch
     */
    private function proxyConfigCurlHandle($ch)
    {
        // Add proxy settings to the curl.
        if ($this->getConfiguration()->getProxyServer()) {
            curl_setopt($ch, CURLOPT_PROXY, $this->getConfiguration()->getProxyServer());
            curl_setopt($ch, CURLOPT_PROXYPORT, $this->getConfiguration()->getProxyPort());

            $username = $this->getConfiguration()->getProxyUser();
            $password = $this->getConfiguration()->getProxyPassword();
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, "$username:$password");
        }
    }
}
