<?php

namespace JiraRestApi\ServiceDesk\Customer;

use JiraRestApi\ClassSerialize;
use JiraRestApi\ServiceDesk\DataObjectTrait;
use JsonSerializable;

class CustomerLinks implements JsonSerializable
{
    use ClassSerialize;
    use DataObjectTrait;

    /**
     * @var string
     */
    public $jiraRest;

    /**
     * @var object
     */
    public $avatarUrls;

    private function setJiraRest(string $jiraRest): void
    {
        $this->jiraRest = $jiraRest;
    }

    private function setAvatarUrls(object $avatarUrls): void
    {
        $this->avatarUrls = $avatarUrls;
    }
}