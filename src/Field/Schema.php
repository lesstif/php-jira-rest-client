<?php

namespace JiraRestApi\Field;

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
