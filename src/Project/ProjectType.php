<?php

namespace JiraRestApi\Project;

use JiraRestApi\ClassSerialize;

class ProjectType
{
    use ClassSerialize;

    public string $key;

    public string $formattedKey;

    public string $descriptionI18nKey;

    public string $icon;

    public string $color;
}
