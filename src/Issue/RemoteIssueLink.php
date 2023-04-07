<?php

namespace JiraRestApi\Issue;

class RemoteIssueLink implements \JsonSerializable
{
    /** @var int */
    public $id;

    /** @var string */
    public $self;

    /** @var string */
    public $globalId;

    /** @var array|null */
    public $application;

    /** @var string|null */
    public $relationship;

    /** @var \JiraRestApi\Issue\RemoteIssueLinkObject|null */
    public $object;

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setUrl(string $url)
    {
        if (is_null($this->object)) {
            $this->object = new self();
        }

        $this->object->url = $url;

        return $this;
    }

    public function setTitle($title)
    {
        $this->object->title = $title;

        return $this;
    }

    public function setSummary($summary)
    {
        $this->object->summary = $summary;

        return $this;
    }

    public function setRelationship($relationship)
    {
        $this->relationship = $relationship;

        return $this;
    }
}
