<?php

namespace JiraRestApi\Epic;

use JiraRestApi\JsonSerializableTrait;

class EpicColor implements \JsonSerializable
{
    use JsonSerializableTrait;

    /** @var string */
    public $key;
}
