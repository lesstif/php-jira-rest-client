<?php

namespace JiraRestApi\IssueLink;

use JiraRestApi\ClassSerialize;

/**
 * Class IssueLinkType.
 *
 * @see https://docs.atlassian.com/jira/REST/server/#api/2/issueLinkType-createIssueLinkType
 */
class IssueLinkType implements \JsonSerializable
{
    use ClassSerialize;

    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $inward;

    /** @var string */
    public $outward;

    /** @var string */
    public $self;

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
