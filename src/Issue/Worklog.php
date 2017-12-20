<?php

namespace JiraRestApi\Issue;

use JiraRestApi\ClassSerialize;
use JiraRestApi\JiraException;

/**
 * Class Worklog.
 */
class Worklog
{
    use ClassSerialize;

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
     * Function to serialize obj vars.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    /**
     * Function to set comments.
     *
     * @param string $comment
     *
     * @return Worklog
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Function to set start time of worklog.
     *
     * @param mixed $started started time value(\DateTime|string)  e.g. -  new DateTime("2016-03-17 11:15:34") or "2016-03-17 11:15:34"
     *
     * @throws JiraException
     *
     * @return Worklog
     */
    public function setStarted($started)
    {
        if (is_string($started)) {
            $dt = new \DateTime($started);
        } elseif ($started instanceof \DateTime) {
            $dt = $started;
        } else {
            throw new JiraException('field only accept date string or DateTime class.'.get_class($started));
        }

        // workround micro second
        $this->started = $dt->format("Y-m-d\TH:i:s").'.000'.$dt->format('O');

        return $this;
    }

    /**
     * Function to set start time of worklog.
     *
     * @param \DateTime $started e.g. -  new DateTime("2014-04-05 16:00:00")
     *
     * @return Worklog
     */
    public function setStartedDateTime($started)
    {
        // workround micro second
        $this->started = $started->format("Y-m-d\TH:i:s").'.000'.$started->format('O');

        return $this;
    }

    /**
     * Function to set worklog time in string.
     *
     * @param string $timeSpent
     *
     * @return Worklog
     */
    public function setTimeSpent($timeSpent)
    {
        $this->timeSpent = $timeSpent;

        return $this;
    }

    /**
     * Function to set worklog time in seconds.
     *
     * @param int $timeSpentSeconds
     *
     * @return Worklog
     */
    public function setTimeSpentSeconds($timeSpentSeconds)
    {
        $this->timeSpentSeconds = $timeSpentSeconds;

        return $this;
    }

    /**
     * Function to set visibility of worklog.
     *
     * @param string $type  value can be group or role
     * @param string $value
     *
     * @return Worklog
     */
    public function setVisibility($type, $value)
    {
        $this->visibility = [
            'type'  => $type,
            'value' => $value,
        ];

        return $this;
    }
}
