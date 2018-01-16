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

    /** @var array */
    public $restrict;

    public function __construct()
    {
        $this->to = [];
        $this->to['users'] = [];
        $this->to['groups'] = [];

        $this->restrict = [];
        $this->restrict['groups'] = [];
        $this->restrict['permissions'] = [];

        $this->to['reporter'] = true;
        $this->to['assignee'] = true;
        $this->to['watchers'] = true;
        $this->to['voters'] = true;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    public function setTextBody($textBody)
    {
        $this->textBody = $textBody;

        return $this;
    }

    public function setHtmlBody($htmlBody)
    {
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

        array_push($this->to['groups'], $group);

        return $this;
    }

    public function setRestrictGroup($groupName)
    {
        $group['name'] = $groupName;

        array_push($this->restrict['groups'], $group);

        return $this;
    }

    public function setRestrictPermission($id, $key)
    {
        $perm['id'] = $id;
        $perm['key'] = $key;
        array_push($this->restrict['permissions'], $perm);

        return $this;
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
