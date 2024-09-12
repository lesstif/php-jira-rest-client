<?php

namespace JiraRestApi\Epic;

use JiraRestApi\JsonSerializableTrait;

class Epic implements \JsonSerializable
{
    use JsonSerializableTrait;

    /** @var int */
    public $id;

    /** @var string */
    public $key;

    /** @var string */
    public $self;

    /** @var string */
    public $name;

    /** @var string */
    public $summary;

    /** @var EpicColor */
    public $color;

    /** @var bool */
    public $done;
}
