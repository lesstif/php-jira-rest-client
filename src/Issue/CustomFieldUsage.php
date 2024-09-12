<?php

namespace JiraRestApi\Issue;

class CustomFieldUsage implements \JsonSerializable
{
    /** @var string */
    public $fieldName;

    /** @var int */
    public $customFieldId;

    /** @var int */
    public $issueCountWithVersionInCustomField;

    public function __construct()
    {
    }

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    public function setCustomFieldId($customFieldId)
    {
        $this->customFieldId = $customFieldId;

        return $this;
    }

    public function setIssueCountWithVersionInCustomField($issueCountWithVersionInCustomField)
    {
        $this->issueCountWithVersionInCustomField = $issueCountWithVersionInCustomField;

        return $this;
    }
}
