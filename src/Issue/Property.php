<?php

declare(strict_types=1);

namespace JiraRestApi\Issue;

use JiraRestApi\ClassSerialize;

class Property
{
    use ClassSerialize;

    public string $key;

    public string $value;
}
