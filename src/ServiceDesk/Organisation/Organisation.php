<?php

declare(strict_types=1);

namespace JiraRestApi\ServiceDesk\Organisation;

use JiraRestApi\ClassSerialize;
use JiraRestApi\ServiceDesk\DataObjectTrait;
use JsonSerializable;

class Organisation implements JsonSerializable
{
    use ClassSerialize;
    use DataObjectTrait;

    public int $id;
    public string $name;
    public array $_links;

    public function setLinks(array $links): void
    {
        $this->_links = $links;
    }
}
