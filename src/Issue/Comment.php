<?php

namespace JiraRestApi\Issue;

class Visibility
{
    public $type;
    public $value;

    public function setType($type)
    {
        $this->type = $type;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getValue()
    {
        return $this->value;
    }
}

class Comment implements \JsonSerializable
{
    /**
     * @var string
     */
    public $self;

    /**
     * @var string
     */
    public $id;

    /**
     * @var \JiraRestApi\Issue\Reporter
     */
    public $author;

    /**
     * @var string
     */
    public $body;

    /**
     * @var \JiraRestApi\Issue\Reporter
     */
    public $updateAuthor;

    /**
     * @var \DateTime
     */
    public $created;

    /**
     * @var \DateTime
     */
    public $updated;

    /**
     * @var \JiraRestApi\Issue\Visibility
     */
    public $visibility;

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function setVisibility($visibility, $value = null)
    {
        if(is_string($visibility) && is_string($value)) {
            $visibility = ['type' => $visibility, 'value' => $value];
        }

        if (is_null($this->visibility)) {
            $this->visibility = new Visibility();
        }

        $visibility = (array) $visibility;

        $this->visibility->setType($visibility['type']);
        $this->visibility->setValue($visibility['value']);

        return $this;
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
