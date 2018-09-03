<?php
/**
 * Created by PhpStorm.
 * User: meshulam
 * Date: 23/09/2017
 * Time: 14:17.
 */

namespace JiraRestApi\Sprint;

use JiraRestApi\ClassSerialize;

class Sprint implements \JsonSerializable
{
    use ClassSerialize;

    /**
     * return only if Project query by key(not id).
     *
     * @var string
     */
    public $expand;

    /* @var string */
    public $self;

    /* @var string */
    public $id;

    /* @var string*/
    public $name;

    /* @var string */
    //public $type;

    /* @var string */
    public $state;

    /* @var DateTime */
    public $startDate;

    /* @var DateTime */
    public $endDate;

    /* @var string */
    public $originBoardId;

    /* @var string */
    public $goal;

    /* @var string */
    public $estimatedVelocity = null;

    /* @var string */
    public $completedVelocity = null;

    // public function __construct() {
    //     $this->startDate = new \DateTime();
    //     $this->endDate = new \DateTime();
    // }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function setName($sprintName)
    {
        $this->name = $sprintName;

        return $sprintName;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getGoal()
    {
        return str_replace(["\n\r", "\n", "\r"], '', $this->goal);
    }

    public function getStartDate($format = 'Y-m-d H:i:s')
    {
        if (!is_null($this->startDate)) {
            $date = new \DateTime($this->startDate);

            return date_format($date, $format);
        }
    }

    public function getEndDate($format = 'Y-m-d H:i:s')
    {
        if (!is_null($this->endDate)) {
            $date = new \DateTime($this->endDate);

            return date_format($date, $format);
        }
    }
}
