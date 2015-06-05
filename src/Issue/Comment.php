<?php

namespace JiraRestApi\Issue;

class Visibility
{
    private $type;
    private $value;

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
    /* @var string */
    public $self;

    /* @var string */
    public $id;

    /* @var Reporter */
    public $author;

    /* @var string */
    public $body;

    /* @var Reporter */
    public $updateAuthor;

    /* @var DateTime */
    public $created;

    /* @var DateTime */
    public $updated;

    /**
     * @var Visibility
     */
    public $visibility;

    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    public function setVisibility($type, $vvalue)
    {
        if (is_null($this->visibility)) {
            $this->visibility = new Visibility();
        }

        $this->visibility->setType($type);
        $this->visibility->setValue($vvalue);

        return $this;
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
