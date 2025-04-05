<?php

namespace JiraRestApi\Request;

use JiraRestApi\Configuration\ConfigurationInterface;
use JiraRestApi\JiraException;
use JiraRestApi\ServiceDeskTrait;
use Psr\Log\LoggerInterface;

class RequestService extends \JiraRestApi\JiraClient
{
    use ServiceDeskTrait;

    private $uri = '/request';

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
    public function __construct(?ConfigurationInterface $configuration = null, ?LoggerInterface $logger = null, $path = './')
    {
        parent::__construct($configuration, $logger, $path);
        $this->setupAPIUri();
    }

    /**
     * Add the given comment to the specified request based on the provided $issueIdOrKey value. Returns a new
     * RequestComment with the response.
     *
     * @param string|int     $issueIdOrKey
     * @param RequestComment $requestComment
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return RequestComment
     */
    public function addComment($issueIdOrKey, RequestComment $requestComment): RequestComment
    {
        $this->log->info("addComment=\n");

        if (empty($requestComment->body)) {
            throw new JiraException('comment param must be an instance of RequestComment and have body text.');
        }

        $data = json_encode($requestComment);

        $ret = $this->exec($this->uri."/$issueIdOrKey/comment", $data);

        $this->log->debug('add comment result='.var_export($ret, true));
        $requestComment = $this->json_mapper->map(
            json_decode($ret),
            new RequestComment()
        );

        return $requestComment;
    }
}
