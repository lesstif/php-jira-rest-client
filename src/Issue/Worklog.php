<?php

namespace JiraRestApi\Issue;

/**
 * Class Worklog
 *
 * @package JiraRestApi\Issue
 */
class Worklog implements \JsonSerializable
{

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     *
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     *       which is a value of any type other than a resource.
     */
    function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

}
