<?php
namespace JiraRestApi\Issue;

class Notify implements \JsonSerializable
{
    /** @var string */
    public $subject;

    /** @var string */
    public $textBody;

    /** @var */
    public $htmlBody;

    /** @var array|null */
    public $to;

    /** @var array|null */
    public $groups;

    public function __construct()
    {
        $this->to = [];
        $this->to['users'] = [];
        $this->groups = [];

        $this->to['reporter'] = false;
        $this->to['assignee'] = false;
        $this->to['watchers'] = true;
        $this->to['voters'] = true;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
        return $this;
    }

    public function setTextBody($textBody) {
        $this->textBody = $textBody;
        return $this;
    }

    public function setHtmlBody($htmlBody) {
        $this->htmlBody = $htmlBody;
        return $this;
    }

    public function sendToReporter($bool)
    {
        $this->to['reporter'] = $bool;
        return $this;
    }

    public function sendToAssignee($bool)
    {
        $this->to['assignee'] = $bool;
        return $this;
    }

    public function sendToWatchers($bool)
    {
        $this->to['watchers'] = $bool;
        return $this;
    }

    public function sendToVoters($bool)
    {
        $this->to['voters'] = $bool;
        return $this;
    }

    public function sendToUser($name, $active)
    {
        $user['name'] = $name;
        $user['active'] = $active;

        array_push($this->to['users'], $user);

        return $this;
    }

    public function sendToGroup($groupName)
    {
        $group['name'] = $groupName;
        // FIXME "self": "http://www.example.com/jira/rest/api/2/group?groupname=notification-group"
        //$group['self'] = $active;

        array_push($this->groups, $group);

        return $this;
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}