<?php

namespace JiraRestApi\Issue;

class Reporter implements \JsonSerializable
{
    /* @var string */
    public $self;

    /* @var string */
    public $name;

    /* @var string */
    public $emailAddress;

    /* @var string */
    public $avatarUrls;

     /* @var string */
    public $displayName;

     /* @var string */
    public $active;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this), function ($value, $key) {
            // allow empty assignee. See https://github.com/lesstif/php-jira-rest-client/issues/18
            if ($key === 'name' && !is_null($value)) {
                return true;
            }
            return !empty($value);
        }, ARRAY_FILTER_USE_BOTH);
    }
}
