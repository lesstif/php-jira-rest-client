<?php
/**
 * Created by PhpStorm.
 * User: meshulam
 * Date: 23/09/2017
 * Time: 14:17.
 */

namespace JiraRestApi\Sprint;

use JiraRestApi\JsonSerializableTrait;

class Sprint implements \JsonSerializable
{
    use JsonSerializableTrait;

    /* @var string */
    public $self;

    /* @var int */
    public $id;

    /* @var string */
    public $name;

    /* @var string */
    public $state;

    /* @var string */
    public $startDate;

    /* @var string */
    public $endDate;

    /* @var string */
    public $completeDate;

    /* @var int */
    public $originBoardId;

    /** @var string */
    public $goal;

    public function setName(string $sprintName): string
    {
        $this->name = $sprintName;

        return $sprintName;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
