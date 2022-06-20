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
    protected ?string $response;

    /**
     * Create a new Jira exception instance.
     */
    public function __construct(?string $message = null, int $code = 0, \Throwable $previous = null, string $response = null)
    {
        parent::__construct($message, $code, $previous);

        $this->response = $response;
    }

    /**
     * Get error response.
     */
    public function getResponse(): ?string
    {
        return $this->response;
    }
}
