<?php

declare(strict_types=1);

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

    public string $createdDate;

    public string $goal;

    public array $issues;

    public function setNameAsString(string $sprintName): self
    {
        $this->name = $sprintName;

        return $this;
    }

    public function setGoalAsString(string $sprintGoal): self
    {
        $this->goal = $sprintGoal;

        return $this;
    }

    public function setOriginBoardIdAsStringOrInt(string|int $originBoardId): self
    {
        $this->originBoardId = strval($originBoardId);

        return $this;
    }

    public function setStartDateAsDateTime(\DateTimeInterface $startDate, string $format = 'Y-m-d'): static
    {
        $this->startDate = $startDate->format($format);

        return $this;
    }

    public function setStartDateAsString(string $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function setEndDateAsDateTime(\DateTimeInterface $endDate, string $format = 'Y-m-d'): static
    {
        $this->endDate = $endDate->format($format);

        return $this;
    }

    public function setEndDateAsString(string $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function setMoveIssues(array $issues): static
    {
        $this->issues = $issues;

        return $this;
    }
}
