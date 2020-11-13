<?php

declare(strict_types=1);

namespace JiraRestApi\Epic;

use JiraRestApi\JsonSerializableTrait;

class EpicColor implements \JsonSerializable
{
    use JsonSerializableTrait;

    /** @var string */
    public $key;
}
