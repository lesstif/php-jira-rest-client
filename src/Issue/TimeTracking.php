<?php
/**
 * Created by PhpStorm.
 * User: keanor
 * Date: 29.07.15
 * Time: 21:27.
 */

namespace JiraRestApi\Issue;

use JiraRestApi\ClassSerialize;

/**
 * Class TimeTracking.
 */
class TimeTracking implements \JsonSerializable
{
    use ClassSerialize;

    /**
     * Original estimate.
     *
     * @var string (ex 90m, 2h, 1d 2h 30m)
     */
    public $originalEstimate;

    /**
     * Remaining estimate.
     *
     * @var string (ex 90m, 2h, 1d 2h 30m)
     */
    public $remainingEstimate;

    /**
     * Time spent.
     *
     * @var string (ex 90m, 2h, 1d 2h 30m)
     */
    public $timeSpent;

    /**
     * Original estimate in seconds, generated in jira
     * for create/update issue set $this->originalEstimate.
     *
     * @var int
     */
    public $originalEstimateSeconds;

    /**
     * Remaining estimate in seconds, generated in jira
     * for create/update issue set $this->remainingEstimate.
     *
     * @var int
     */
    public $remainingEstimateSeconds;

    /**
     * Time spent in seconds, generated in jira
     * for create/update issue set $this->timeSpent.
     *
     * @var int
     */
    public $timeSpentSeconds;

    /**
     * @return string
     */
    public function getOriginalEstimate()
    {
        return $this->originalEstimate;
    }

    /**
     * @param string $originalEstimate
     */
    public function setOriginalEstimate($originalEstimate)
    {
        $this->originalEstimate = $originalEstimate;
    }

    /**
     * @return string
     */
    public function getRemainingEstimate()
    {
        return $this->remainingEstimate;
    }

    /**
     * @param string $remainingEstimate
     */
    public function setRemainingEstimate($remainingEstimate)
    {
        $this->remainingEstimate = $remainingEstimate;
    }

    /**
     * @return string
     */
    public function getTimeSpent()
    {
        return $this->timeSpent;
    }

    /**
     * @param string $timeSpent
     */
    public function setTimeSpent($timeSpent)
    {
        $this->timeSpent = $timeSpent;
    }

    /**
     * @return int
     */
    public function getOriginalEstimateSeconds()
    {
        return $this->originalEstimateSeconds;
    }

    /**
     * @param int $originalEstimateSeconds
     */
    public function setOriginalEstimateSeconds($originalEstimateSeconds)
    {
        $this->originalEstimateSeconds = $originalEstimateSeconds;
    }

    /**
     * @return int
     */
    public function getRemainingEstimateSeconds()
    {
        return $this->remainingEstimateSeconds;
    }

    /**
     * @param int $remainingEstimateSeconds
     */
    public function setRemainingEstimateSeconds($remainingEstimateSeconds)
    {
        $this->remainingEstimateSeconds = $remainingEstimateSeconds;
    }

    /**
     * @return int
     */
    public function getTimeSpentSeconds()
    {
        return $this->timeSpentSeconds;
    }

    /**
     * @param int $timeSpentSeconds
     */
    public function setTimeSpentSeconds($timeSpentSeconds)
    {
        $this->timeSpentSeconds = $timeSpentSeconds;
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *               which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
