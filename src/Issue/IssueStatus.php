<?php

namespace JiraRestApi\Issue;

class IssueStatus implements \JsonSerializable
{
    /* @var string */
    public $self;

    /* @var string */
    public $id;

    /* @var string|null */
    public ?string $description = null;

    /* @var string */
    public $iconUrl;

    /* @var string */
    public $name;

    /* @var string|null status name in English, regardless of the user's language (Jira Cloud only) */
    public ?string $untranslatedName = null;

    /* @var \JiraRestApi\Issue\Statuscategory */
    public $statuscategory;

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
