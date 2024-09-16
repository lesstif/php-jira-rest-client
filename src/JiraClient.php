<?php

namespace JiraRestApi;

use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\Configuration\DotEnvConfiguration;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Interact jira server with REST API.
 */
class JiraClient
{
    public string $cookieFile;

    /**
     * Json Mapper.
     */
    protected \JsonMapper $json_mapper;

    /**
     * HTTP response code.
     */
    protected string|int $http_response;

    /**
     * JIRA REST API URI.
     */
    private string $api_uri = '/rest/api/2';

    /**
     * CURL instance.
     */
    protected \CurlHandle $curl;

    /**
     * Monolog instance.
     */
    protected LoggerInterface $log;

    /**
     * Jira Rest API Configuration.
     */
    protected ConfigurationInterface $configuration;

    /**
     * json en/decode options.
     */
    protected int $jsonOptions;

    /**
     * Constructor.
     *
     * @param ConfigurationInterface|null $configuration
     * @param LoggerInterface|null        $logger
     * @param string                      $path
     *
     * @throws JiraException
     */
    public function __construct(ConfigurationInterface $configuration = null, LoggerInterface $logger = null, string $path = './')
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
                    $this->configuration->getJiraLogLevel()
                ));
            }
        } else {
            $this->log = new Logger('JiraClient');

            // Monolog 3.x has a breaking change, so I have to add this dirty code.
            $ver = \Composer\InstalledVersions::getVersion('monolog/monolog');
            $major = intval(explode('.', $ver)[0]);

            if ($major === 2) {
                $this->log->pushHandler(new NoOperationMonologHandler());
            } elseif ($major === 3) {
                $this->log->pushHandler(new NoOperationMonologHandlerV3());
            } else {
                throw new JiraException("Unsupported Monolog version $major");
            }
        }

        $this->http_response = 200;

        $this->curl = curl_init();

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
     * @param \CurlHandle|bool $ch
     * @param array            $curl_http_headers
     * @param string|null      $cookieFile
     *
     * @return array
     */
    public function curlPrepare(\CurlHandle|bool $ch, array $curl_http_headers, ?string $cookieFile): array
    {
        $this->authorization($ch, $curl_http_headers, $cookieFile);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->getConfiguration()->isCurlOptSslVerifyHost());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getConfiguration()->isCurlOptSslVerifyPeer());
        if ($this->getConfiguration()->isCurlOptSslCert()) {
            curl_setopt($ch, CURLOPT_SSLCERT, $this->getConfiguration()->isCurlOptSslCert());
        }
        if ($this->getConfiguration()->isCurlOptSslCertPassword()) {
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->getConfiguration()->isCurlOptSslCertPassword());
        }
        if ($this->getConfiguration()->isCurlOptSslKey()) {
            curl_setopt($ch, CURLOPT_SSLKEY, $this->getConfiguration()->isCurlOptSslKey());
        }
        if ($this->getConfiguration()->isCurlOptSslKeyPassword()) {
            curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $this->getConfiguration()->isCurlOptSslKeyPassword());
        }
        if ($this->getConfiguration()->getTimeout()) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->getConfiguration()->getTimeout());
        }

        return $curl_http_headers;
    }

    /**
     * Serialize only not null field.
     */
    protected function filterNullVariable(array $haystack): array
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
     * @param string            $context        Rest API context (ex.:issue, search, etc..)
     * @param array|string|null $post_data
     * @param string|null       $custom_request [PUT|DELETE]
     * @param string|null       $cookieFile     cookie file
     *
     * @throws JiraException
     *
     * @return string|bool
     */
    public function exec(string $context, array|string $post_data = null, string $custom_request = null, string $cookieFile = null): string|bool
    {
        $url = $this->createUrlByContext($context);

        if (is_string($post_data)) {
            $this->log->info("Curl $custom_request: $url JsonData=".$post_data);
        } elseif (is_array($post_data)) {
            $this->log->info("Curl $custom_request: $url JsonData=".json_encode($post_data, JSON_UNESCAPED_UNICODE));
        }

        curl_reset($this->curl);
        $ch = $this->curl;
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->configuration->getTimeout());
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->configuration->getTimeout());
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

        // save HTTP Headers
        $curl_http_headers = [
            'Accept: */*',
            'Content-Type: application/json',
            'X-Atlassian-Token: no-check',
            'X-ExperimentalApi: opt-in',    // for JSM
        ];

        $curl_http_headers = $this->curlPrepare($ch, $curl_http_headers, $cookieFile);

        curl_setopt($ch, CURLOPT_USERAGENT, $this->getConfiguration()->getCurlOptUserAgent());

        // curl_setopt(): CURLOPT_FOLLOWLOCATION cannot be activated when an open_basedir is set
        if (!function_exists('ini_get') || !ini_get('open_basedir')) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }

        // remove for avoid https://github.com/php/php-src/issues/14184
        //curl_setopt($ch, CURLOPT_ENCODING, '');

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            $curl_http_headers
        );

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
                    ."\nError Message : ".$response, $this->http_response, null, $response);
            }
        }

        return $response;
    }

    /**
     * Create upload handle.
     */
    private function createUploadHandle(string $url, string $upload_file, \CurlHandle $ch): \CurlHandle
    {
        $curl_http_headers = [
            'Accept: */*',
            'Content-Type: multipart/form-data',
            'X-Atlassian-Token: no-check',
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        // send file
        curl_setopt($ch, CURLOPT_POST, true);

        if (PHP_MAJOR_VERSION == 5 && PHP_MINOR_VERSION < 5) {
            $attachments = realpath($upload_file);
            $filename = basename($upload_file);

            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                ['file' => '@'.$attachments.';filename='.$filename]
            );

            $this->log->debug('using legacy file upload');
        } else {
            // CURLFile require PHP > 5.5
            $attachments = new \CURLFile(realpath($upload_file));
            $attachments->setPostFilename(basename($upload_file));

            curl_setopt(
                $ch,
                CURLOPT_POSTFIELDS,
                ['file' => $attachments]
            );

            $this->log->debug('using CURLFile='.var_export($attachments, true));
        }

        $this->authorization($ch, $curl_http_headers);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->getConfiguration()->isCurlOptSslVerifyHost());
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->getConfiguration()->isCurlOptSslVerifyPeer());

        if ($this->getConfiguration()->isCurlOptSslCert()) {
            curl_setopt($ch, CURLOPT_SSLCERT, $this->getConfiguration()->isCurlOptSslCert());
        }
        if ($this->getConfiguration()->isCurlOptSslCertPassword()) {
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->getConfiguration()->isCurlOptSslCertPassword());
        }
        if ($this->getConfiguration()->isCurlOptSslKey()) {
            curl_setopt($ch, CURLOPT_SSLKEY, $this->getConfiguration()->isCurlOptSslKey());
        }
        if ($this->getConfiguration()->isCurlOptSslKeyPassword()) {
            curl_setopt($ch, CURLOPT_SSLKEYPASSWD, $this->getConfiguration()->isCurlOptSslKeyPassword());
        }
        if ($this->getConfiguration()->getTimeout()) {
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->getConfiguration()->getTimeout());
        }

        $this->proxyConfigCurlHandle($ch);

        // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); cannot be activated when an open_basedir is set
        if (!function_exists('ini_get') || !ini_get('open_basedir')) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_http_headers);

        curl_setopt($ch, CURLOPT_VERBOSE, $this->getConfiguration()->isCurlOptVerbose());

        $this->log->debug('Curl exec='.$url);

        return $ch;
    }

    /**
     * File upload.
     */
    public function upload(string $context, array $filePathArray): array
    {
        $url = $this->createUrlByContext($context);

        $results = [];

        $ch = curl_init();

        $idx = 0;
        foreach ($filePathArray as $file) {
            $this->createUploadHandle($url, $file, $ch);

            $response = curl_exec($ch);

            // if request failed or have no result.
            if (!$response) {
                $http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $body = curl_error($ch);

                if ($http_response === 204 || $http_response === 201 || $http_response === 200) {
                    $results[$idx] = $response;
                } else {
                    $msg = sprintf('CURL Error: http response=%d, %s', $http_response, $body);
                    $this->log->error($msg);

                    curl_close($ch);

                    throw new JiraException($msg);
                }
            } else {
                $results[$idx] = $response;
            }
            $idx++;
        }

        curl_close($ch);

        return $results;
    }

    protected function closeCURLHandle(array $chArr, \CurlMultiHandle $mh, string $body, int $result_code): void
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
     */
    protected function createUrlByContext(string $context): string
    {
        $host = $this->getConfiguration()->getJiraHost();

        return $host.$this->api_uri.'/'.preg_replace('/\//', '', $context, 1);
    }

    /**
     * Add authorize to curl request.
     */
    protected function authorization(\CurlHandle $ch, array &$curl_http_headers, string $cookieFile = null): void
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
        if (!is_string($cookieFile) || !file_exists($cookieFile)) {
            if ($this->getConfiguration()->isTokenBasedAuth() === true) {
                $curl_http_headers[] = 'Authorization: Bearer '.$this->getConfiguration()->getPersonalAccessToken();
            } else {
                $username = $this->getConfiguration()->getJiraUser();
                $password = $this->getConfiguration()->getJiraPassword();
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            }
        }
    }

    /**
     * Jira Rest API Configuration.
     */
    public function getConfiguration(): ConfigurationInterface
    {
        return $this->configuration;
    }

    /**
     * Set a custom Jira API URI for the request.
     *
     * @param string $api_uri
     */
    public function setAPIUri(string $api_uri): string
    {
        $this->api_uri = $api_uri;

        return $this->api_uri;
    }

    /**
     * convert to query array to http query parameter.
     */
    public function toHttpQueryParameter(array $paramArray, bool $dropNullKey = true): string
    {
        $queryParam = '?';

        foreach ($paramArray as $key => $value) {
            if ($dropNullKey === true && empty($value)) {
                continue;
            }
            $v = null;

            // some param field(Ex: expand) type is array.
            if (is_array($value)) {
                $v = implode(',', $value);
            } else {
                $v = $value;
            }

            $queryParam .= rawurlencode($key).'='.rawurlencode($v).'&';
        }

        return $queryParam;
    }

    /**
     * download and save into outDir.
     */
    public function download(string $url, string $outDir, string $file, string $cookieFile = null): mixed
    {
        $curl_http_header = [
            'Accept: */*',
            'Content-Type: application/json',
            'X-Atlassian-Token: no-check',
        ];

        $file = fopen($outDir.DIRECTORY_SEPARATOR.urldecode($file), 'w');

        curl_reset($this->curl);
        $ch = $this->curl;
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        // output to file handle
        curl_setopt($ch, CURLOPT_FILE, $file);

        $curl_http_header = $this->curlPrepare($ch, $curl_http_header, $cookieFile);

        $this->proxyConfigCurlHandle($ch);

        // curl_setopt(): CURLOPT_FOLLOWLOCATION cannot be activated when an open_basedir is set
        if (!function_exists('ini_get') || !ini_get('open_basedir')) {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        }

        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            $curl_http_header
        );

        curl_setopt($ch, CURLOPT_VERBOSE, $this->getConfiguration()->isCurlOptVerbose());

        $this->log->debug('Curl exec='.$url);
        $response = curl_exec($ch);

        // if request failed.
        if (!$response) {
            $this->http_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $body = curl_error($ch);
            fclose($file);

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
            fclose($file);

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
     */
    public function setCookieFile(string $cookieFile): static
    {
        $this->cookieFile = $cookieFile;

        return $this;
    }

    /**
     * Config a curl handle with proxy configuration (if set) from ConfigurationInterface.
     */
    private function proxyConfigCurlHandle(\CurlHandle $ch): void
    {
        // Add proxy settings to the curl.
        if ($this->getConfiguration()->getProxyServer()) {
            curl_setopt($ch, CURLOPT_PROXY, $this->getConfiguration()->getProxyServer());
            curl_setopt($ch, CURLOPT_PROXYPORT, $this->getConfiguration()->getProxyPort());

            $username = $this->getConfiguration()->getProxyUser();
            $password = $this->getConfiguration()->getProxyPassword();
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, "$username:$password");
        }

        // Set the proxy type for curl, default is CURLPROXY_HTTP (0)
        if ($this->getConfiguration()->getProxyType()) {
            curl_setopt($ch, CURLOPT_PROXYTYPE, $this->getConfiguration()->getProxyType());
        }
    }

    /**
     * setting REST API url to V2.
     *
     * @return $this
     */
    public function setRestApiV2()
    {
        $this->api_uri = '/rest/api/2';

        return $this;
    }

    /**
     * setting JSON en/decoding options.
     */
    public function setJsonOptions(int $jsonOptions): static
    {
        $this->jsonOptions = $jsonOptions;

        return $this;
    }

    /**
     * get json en/decode options.
     */
    public function getJsonOptions(): int
    {
        return $this->jsonOptions;
    }

    public function getHttpResponse(): string|int
    {
        return $this->http_response;
    }
}
