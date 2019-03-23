<?php

namespace JiraRestApi\Issue;

use JiraRestApi\ClassSerialize;

class Reporter implements \JsonSerializable
{
    use ClassSerialize;

    /** @var string */
    public $self;

    /** @var string */
    public $name;

    /** @var string */
    public $emailAddress;

    /** @var array|null */
    public $avatarUrls;

    /** @var string */
    public $displayName;

    /** @var string */
    public $active;

    // want assignee to unassigned
    private $wantUnassigned = false;

    /** @var string */
    public $accountId;

    public function jsonSerialize()
    {
        $vars = (get_object_vars($this));

        foreach ($vars as $key => $value) {
            if ($key === 'name' && ($this->isWantUnassigned() === true)) {
                continue;
            } elseif ($key === 'wantUnassigned') {
                unset($vars[$key]);
            } elseif (is_null($value) || $value === '') {
                unset($vars[$key]);
            }
        }

        if (empty($vars)) {
            return;
        }

        return $vars;
    }

    /**
     * determine class has value for effective json serialize.
     *
     * @see https://github.com/lesstif/php-jira-rest-client/issues/126
     *
     * @return bool
     */
    public function isEmpty()
    {
        if (empty($this->name) && empty($this->self)) {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isWantUnassigned()
    {
        if ($this->wantUnassigned) {
            return true;
        }

        return false;
    }

    /**
     * @param $param boolean
     */
    public function setWantUnassigned($param)
    {
        $this->wantUnassigned = $param;
        $this->name = null;
    }
}
