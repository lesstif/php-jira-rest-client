<?php

namespace JiraRestApi\Issue;

class Comment implements \JsonSerializable
{
    /** @var string */
    public $self;

    /** @var string */
    public $id;

    /** @var Reporter */
    public $author;

    /** @var string */
    public $body;

    /** @var Reporter */
    public $updateAuthor;

    /** @var \DateTime */
    public $created;

    /** @var \DateTime */
    public $updated;

    /** @var Visibility */
    public $visibility;

    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @param Visibility $type
     * @param null       $value
     *
     * @return $this
     */
    public function setVisibility($type, $value = null)
    {
        if (is_null($this->visibility)) {
            $this->visibility = new Visibility();
        }

        if (is_array($type)) {
            $this->visibility->setType($type['type']);
            $this->visibility->setValue($type['value']);
        } elseif ($type instanceof Visibility) {
            $this->visibility = $type;
        } else {
            $this->visibility->setType($type);
            $this->visibility->setValue($value);
        }

        return $this;
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
