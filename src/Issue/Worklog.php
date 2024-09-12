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
    use VisibilityTrait;

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
     * @var mixed
     *
     * API V2 accepts a string, whereas API V3 requires an Atlassian Document
     * Format, defined in this project by the ContentField class.
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
     * @var \JiraRestApi\Issue\Visibility
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
     * @param mixed $comment
     *
     * @return Worklog
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    // Note that in the docblock below, you cannot replace `mixed` by `\DateTimeInterface|string` because JsonMapper doesn't support that,
    // see <https://github.com/cweiske/jsonmapper/issues/64#issuecomment-269545585>.

    /**
     * Function to set start time of worklog.
     *
     * @param mixed $started started time value(\DateTimeInterface|string)  e.g. -  new \DateTime("2016-03-17 11:15:34") or "2016-03-17 11:15:34"
     *
     * @throws JiraException
     *
     * @return Worklog
     */
    public function setStarted($started)
    {
        if (is_string($started)) {
            $dt = new \DateTime($started);
        } elseif ($started instanceof \DateTimeInterface) {
            $dt = $started;
        } else {
            throw new JiraException('field only accept date string or DateTimeInterface object.'.get_class($started));
        }

        // workround micro second
        $this->started = $dt->format("Y-m-d\TH:i:s").'.000'.$dt->format('O');

        return $this;
    }

    /**
     * Function to set start time of worklog.
     *
     * @param \DateTimeInterface $started e.g. -  new \DateTime("2014-04-05 16:00:00")
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
}
