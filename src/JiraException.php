<?php
/**
 * Created by PhpStorm.
 * User: keanor
 * Date: 14.08.15
 * Time: 20:53.
 */

namespace JiraRestApi;

/**
 * Class JiraException.
 */
class JiraException extends \Exception
{
    /**
     * Response returned by Jira.
     *
     * @var string|null
     */
    protected $response;

    /**
     * Create a new Jira exception instance.
     *
     * @param string     $message
     * @param int        $code
     * @param \Throwable $previous
     * @param string     $response
     *
     * @return void
     */
    public function __construct($message = null, $code = 0, \Throwable $previous = null, $response = null)
    {
        parent::__construct($message, $code, $previous);

        $this->response = $response;
    }

    /**
     * Get error response.
     *
     * @return string|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
