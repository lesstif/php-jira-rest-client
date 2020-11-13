<?php

declare(strict_types=1);

namespace JiraRestApi\Field;

use JiraRestApi\ClassSerialize;

/**
 * Class Field.
 *
 * Jira Field Object mapper
 */
class Field implements \JsonSerializable
{
    use ClassSerialize;

    /**
     * only custom field.
     */
    const CUSTOM = 1;

    /**
     * only system field.
     */
    const SYSTEM = 2;

    /**
     * both System and Custom.
     */
    const BOTH = 3;

    /**
     * @param string $name
     *
     * @return $this
     */
    public function set_Name(string $name): Field
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function set_Description(string $description): Field
    {
        $this->description = $description;

        return $this;
    }

    /**
     * set custom field type.
     *
     * @see https://confluence.atlassian.com/jira064/changing-custom-field-types-720415917.html
     *
     * @param string $type
     *
     * @return $this
     */
    public function set_Type(string $type): Field
    {
        $this->type = $type;

        return $this;
    }

    /**
     * atlassian supplied poor documentation.
     *
     * @param string $searcherKey
     *
     * @return $this
     */
    public function set_SearcherKey(string $searcherKey): Field
    {
        $this->searcherKey = $searcherKey;

        return $this;
    }

    /* @var string */
    public $id;

    /* @var string */
    public $name;

    /* @var string */
    public $description;

    /* @var string */
    public $type;

    /* @var boolean */
    public $custom;

    /* @var boolean */
    public $orderable;

    /* @var boolean */
    public $navigable;

    /* @var boolean */
    public $searchable;

    /** @var string */
    public $searcherKey;

    /**
     * if field is custom, array has two element. first is custom field number represented in bracket with cf prefix, second element is field name.
     *
     * Ex: [0 => "cf[10201]", 1 => "My Check Box"]
     *
     * @var array
     */
    public $clauseNames;

    /* @var Schema */
    public $schema;

    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }
}
