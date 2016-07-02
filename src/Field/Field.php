<?php

namespace JiraRestApi\Field;
use JiraRestApi\ClassSerialize;

/**
 * Custom filed schema.
 *
 * Class Schema
 */
class Schema
{
    /* @var string */

    /**
     * custom filed type.
     *
     * @var string
     */
    public $type;

    /** i don't know what this means.
     * @var string
     */
    public $items;

    /* jira custom field component class full namespace.

     * Ex:  "com.atlassian.jira.plugin.system.customfieldtypes:multicheckboxes",
     * "com.atlassian.jira.plugin.system.customfieldtypes:select"
     *
     * @var string
     */
    public $custom;

    /** custom filed id. Ex: 10201
     * @var string
     */
    public $customId;
}

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

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * set custom field type.
     *
     * @see https://confluence.atlassian.com/jira064/changing-custom-field-types-720415917.html
     *
     * @param $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * atlassian supplied poor documentation.
     *
     * @param $searcherKey
     *
     * @return $this
     */
    public function setSearcherKey($searcherKey)
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

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }
}
