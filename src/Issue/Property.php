<?php

declare(strict_types=1);

namespace JiraRestApi\Issue;

use JiraRestApi\ClassSerialize;

class Property
{
    use ClassSerialize;

    /** @var string */
    public $key;

    /** @var string */
    public $value;
}
