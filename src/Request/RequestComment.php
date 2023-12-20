<?php

namespace JiraRestApi\Request;

class RequestComment implements \JsonSerializable
{
    /** @var string */
    public $id;

    /** @var string */
    public $body;

    /** @var bool */
    public $public;

    /** @var \JiraRestApi\Request\Author */
    public $author;

    /** @var \DateTimeInterface */
    public $created;

    /**
     * @param string $body
     *
     * @return $this
     */
    public function setBody(string $body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @param bool $public True for is public, false otherwise
     *
     * @return $this
     */
    public function setIsPublic(bool $public)
    {
        $this->public = $public;

        return $this;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this), function ($var) {
            return $var !== null;
        });
    }
}
