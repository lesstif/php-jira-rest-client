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

    public string $self;

    public string $id;

    public string $name;

    public string $state;

    public string $startDate;

    public string $endDate;

    public string $activatedDate;

    public string $completeDate;

    public string $originBoardId;

    public string $goal;

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
