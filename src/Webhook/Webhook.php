<?php

namespace JiraRestApi\Webhook;

class Webhook implements \JsonSerializable
{
    const JIRA_ISSUE_CREATED = 'jira:issue_created';
    const JIRA_ISSUE_DELETED = 'jira:issue_deleted';
    const JIRA_ISSUE_UPDATED = 'jira:issue_updated';
    const JIRA_WORKLOG_UPDATED = 'jira:worklog_updated';
    const JIRA_PROJECT_CREATED = 'project_created';
    const JIRA_PROJECT_UPDATED = 'project_updated';
    const JIRA_PROJECT_DELETED = 'project_deleted';
    const JIRA_USER_CREATED = 'user_created';
    const JIRA_USER_UPDATED = 'user_updated';
    const JIRA_USER_DELETED = 'user_deleted';

    /**
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $name;

    /**
     * @var object
     */
    public $excludeBody;

    /**
     * @var object
     */
    public $filters;

    /**
     * @var array
     */
    public $events;

    /**
     * @var int
     */
    public $enabled;

    /**
     * @var string
     */
    public $self;

    /**
     * @var string
     */
    public $lastUpdatedUser;

    /**
     * @var string
     */
    public $lastUpdatedDisplayName;

    /**
     * @var string
     */
    public $lastUpdated;

    protected $jqlFilter;

    protected $excludeIssueDetails;

    public function getId()
    {
        if (!is_null($this->self)) {
            $parts = explode("/", $this->self);
            return (int)end($parts);
        }

        return null;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setEvents($events)
    {
        $allowed_hooks = [
            self::JIRA_ISSUE_CREATED,
            self::JIRA_ISSUE_DELETED,
            self::JIRA_ISSUE_UPDATED,
            self::JIRA_WORKLOG_UPDATED,
            self::JIRA_PROJECT_CREATED,
            self::JIRA_PROJECT_UPDATED,
            self::JIRA_PROJECT_DELETED,
            self::JIRA_USER_CREATED,
            self::JIRA_USER_UPDATED,
            self::JIRA_USER_DELETED,
        ];

        if(is_array($events)) {
            foreach($events as $event) {
                if(in_array($event, $allowed_hooks)) {
                    $this->events[] = $event;
                }
            }
        }

        if(is_string($events)) {
            if(in_array($events, $allowed_hooks)) {
                $this->events[] = $events;
            }
        }

        // prevent duplicates
        $this->events = array_unique($this->events);
        return $this;
    }

    public function setJqlFilter($filter)
    {
        $this->jqlFilter = $filter;
        return $this;
    }

    public function setExcludeIssueDetails($excludeIssueDetails = false)
    {
        $this->excludeIssueDetails = $excludeIssueDetails == false
            ? false
            : true;

        return $this;
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}