<?php

namespace JiraRestApi\Project;

use JiraRestApi\ClassSerialize;

class ProjectType
{
    use ClassSerialize;

    /** @var string */
    public $key;

    /** @var string */
    public $formattedKey;

    /** @var string */
    public $descriptionI18nKey;

    /** @var string */
    public $icon;

    /** @var string */
    public $color;
}
