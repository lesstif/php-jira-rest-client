<?php

namespace JiraRestApi\Issue;

/**
 * Class Worklog.
 */
class Worklog {

    /**
     * @var int id of worklog 
     */
    public $id;

    /**
     * @var string api link of worklog
     */
    public $self;

    /**
     * @var array details about author
     */
    public $author;

    /**
     * @var array 
     */
    public $updateAuthor;

    /**
     * @var string 
     */
    public $updated;

    /**
     * @var string 
     */
    public $timeSpent;

    /**
     * @var string 
     */
    public $issueId;

    /**
     * @var string 
     */
    public $comment;

    /**
     * @var string 
     */
    public $started;

    /**
     * @var int 
     */
    public $timeSpentSeconds;

    /**
     * @var array 
     */
    public $visibility;

    /**
     * Function to serialize obj vars
     * @return array
     */
    public function jsonSerialize() {
        return array_filter(get_object_vars($this));
    }

    /**
     * Function to set comments
     * @param string $comment
     */
    public function setComment($comment) {
        $this->comment = $comment;
    }

    /**
     * Function to set start time of worklog
     * @param string $started e.g. -  "2016-06-20T15:37:17.211+0000"
     */
    public function setStarted($started) {
        $this->started = $started;
    }

    /**
     * Function to set worklog time in seconds
     * @param int $timeSpentSeconds
     */
    public function setTimeSpentSeconds($timeSpentSeconds) {
        $this->timeSpentSeconds = $timeSpentSeconds;
    }

    /**
     * Function to set visibility of worklog
     * @param string $type value can be group or role
     * @param string $value
     */
    public function setVisibility($type, $value) {
        $this->visibility = [
            'type' => $type,
            'value' => $value,
        ];
    }

}
