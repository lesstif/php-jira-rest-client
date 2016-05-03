<?php

namespace JiraRestApi\Issue;

class Reporter implements \JsonSerializable
{
    /**
     * @var string
     */
    public $self;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $emailAddress;

    /**
     * @var array
     */
    public $avatarUrls;

    /**
     * @var string
     */
    public $displayName;

    /**
     * @var bool
     */
    public $active;

    public function jsonSerialize()
    {
        $vars = (get_object_vars($this));
        foreach ($vars as $key => $value) {
            if ($key === 'name' && !is_null($value)) {
                continue;
            } elseif (is_null($value) || $value === '') {
                unset($vars[$key]);
            }
        }
        if (empty($vars)) {
            return;
        }

        return $vars;
    }
}
