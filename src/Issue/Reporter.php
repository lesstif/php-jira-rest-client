<?php

namespace JiraRestApi\Issue;

use JiraRestApi\ClassSerialize;

class Reporter implements \JsonSerializable
{
    use ClassSerialize;

    public string $key;

    public string $timezone;

    public string $self;

    public ?string $name;

    public ?string $emailAddress = null;

    public array $avatarUrls;

    public string $displayName;

    public string $active;

    // want assignee to unassigned
    private bool $wantUnassigned = false;

    public string $accountId;

    public string $locale;

    public array $groups;

    public array $applicationRoles;

    public string $expand;

    public bool $deleted;

    public string $accountType;

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): ?array
    {
        $vars = get_object_vars($this);

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
            return null;
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
     * @param bool $param boolean
     */
    public function setWantUnassigned(bool $param)
    {
        $this->wantUnassigned = $param;
        $this->name = null;
    }
}
