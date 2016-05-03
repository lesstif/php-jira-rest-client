<?php

namespace JiraRestApi\Issue;

class TransitionTo
{
    /**
     * @var string
     */
    public $self;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $iconUrl;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $id;

    /**
     * @var array
     */
    public $statusCategory;
}

/**
 * Issue Transition mapping class.
 */
class Transition implements \JsonSerializable
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var TransitionTo
     */
    public $to;

    /**
     * @var array
     */
    public $fields;

    /**
     * @var IssueField
     */
    public $issueFields;

    /**
     * @var array
     */
    public $transition;

    public $update;

    public function setTransitionName($name)
    {
        if (is_null($this->transition)) {
            $this->transition = [];
        }

        $this->transition['name'] = $name;
    }

    public function setTransitionId($id)
    {
        if (is_null($this->transition)) {
            $this->transition = [];
        }

        $this->transition['id'] = $id;
    }

    public function setCommentBody($commentBody)
    {
        if (is_null($this->update)) {
            $this->update = [];
            $this->update['comment'] = [];
        }

        $ar = [];
        $ar['add']['body'] = $commentBody;
        array_push($this->update['comment'], $ar);
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
