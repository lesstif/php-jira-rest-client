<?php

namespace JiraRestApi\Issue;

use DateTime;
use DateTimeInterface;

class Version implements \JsonSerializable
{
    public string $self;

    public string $id;

    // Version name: ex: 4.2.3
    public ?string $name;

    // version description: ex; improvement performance
    public ?string $description;

    public bool $archived;

    public bool $released;

    public \DateTimeInterface|string $releaseDate;

    public bool $overdue;

    public ?string $userReleaseDate;

    public int $projectId;

    public function __construct($name = null)
    {
        $this->name = $name;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function setProjectId($id): static
    {
        $this->projectId = $id;

        return $this;
    }

    public function setName($name): static
    {
        $this->name = $name;

        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    public function setArchived($archived): static
    {
        $this->archived = $archived;

        return $this;
    }

    public function setReleased($released): static
    {
        $this->released = $released;

        return $this;
    }

    public function setReleaseDate(DateTimeInterface $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function setUserReleaseDateAsDateTime($userReleaseDate): static
    {
        $this->userReleaseDate = $userReleaseDate;

        return $this;
    }
}
