<?php

namespace JiraRestApi\Issue;

class Issue implements \JsonSerializable
{
    /**
     * return only if Project query by key(not id).
     */
    public ?string $expand = null;

    public string $self;

    public string $id;

    public string $key;

    public IssueField $fields;

    public ?array $renderedFields = null;

    public ?array $names = null;

    public ?array $schema = null;

    public ?array $transitions = null;

    public ?array $operations = null;

    public ?array $editmeta = null;

    public ?ChangeLog $changelog = null;

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
