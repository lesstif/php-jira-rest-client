<?php
/**
 * Created by PhpStorm.
 * User: meshulam
 * Date: 23/09/2017
 * Time: 14:17.
 */

namespace JiraRestApi\Sprint;

class Sprint implements \JsonSerializable
{
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

    /* @var string */
    public $startDate;

    /* @var string */
    public $endDate;

    /* @var string */
    public $originalBoardiD;

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
}
