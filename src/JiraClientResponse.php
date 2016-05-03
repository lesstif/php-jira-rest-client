<?php

namespace JiraRestApi;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;

class JiraClientResponse
{
    protected $originalResponse;
    protected $response;

    /**
     * raw response
     */
    protected $raw;

    /**
     * interpreted response array
     */
    protected $rawData = [];

    /**
     * error code if available
     */
    protected $code;

    /**
     * error message if present
     */
    protected $error;

    /**
     * @var LoggerInterface
     */
    protected $log;

    /**
     * AbstractResponse constructor.
     * @param ResponseInterface $raw
     */
    public function __construct(ResponseInterface $raw, LoggerInterface $logger)
    {
        $this->raw = $raw;
        $this->log = $logger;
    }

    /**
     * @return bool
     */
    public function parse()
    {
        /** @var StreamInterface $body */
        $body = $this->raw->getBody();
        $content = (string) $body;
        $content = json_decode($content, true);

        $this->code = $this->raw->getStatusCode();

        if (in_array($this->code, [200, 201, 204])) {
            $this->rawData = $content;
        } else {
            $this->error = $content;
            $this->log->error('JiraRestApi parse response error: ', [$this->error]);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @param $rawData
     */
    public function setRawData(array $rawData)
    {
        $this->rawData = array_merge_recursive($this->rawData, $rawData);
    }

    /**
     * @return ResponseInterface
     */
    public function getRaw()
    {
        return $this->raw;
    }

    /**
     * @return ResponseInterface
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return ResponseInterface
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return ResponseInterface
     */
    public function hasErrors()
    {
        return !empty($this->error);
    }

    /**
     * @param $message
     *
     * @return $this
     */
    public function setError($message)
    {
        if(is_array($this->error) && isset($this->error['errorMessages'])) {
            $this->error['errorMessages'][] = $message;
        } else {
            $this->error = ['errorMessages' => [$message]];
        }

        return $this;
    }

    /**
     * @param $name
     * @param $args
     * @return mixed
     * @throws \Exception
     */
    public function __call($name, $args)
    {
        $key = strtolower(str_replace('get', '', $name));

        if (isset($this->rawData[$key])) {
            return $this->rawData[$key];
        }

        throw new \Exception('JiraApi: unknown field ' . $key);
    }
}