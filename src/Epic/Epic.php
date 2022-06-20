<?php

namespace JiraRestApi\Epic;

use JiraRestApi\JsonSerializableTrait;

class Epic implements \JsonSerializable
{
    use JsonSerializableTrait;

    public int $id;
    public string $key;
    public string $self;
    public string $name;

    public string $summary;

    public EpicColor $color;

    public bool $done;
}
