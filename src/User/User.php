<?php

namespace JiraRestApi\User;

use JiraRestApi\Issue\Reporter;

/**
 * Description of User.
 *
 * @author Anik
 */
class User extends Reporter
{
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }

    /**
     * User constructor.
     *
     * @param array $array user info array.
     */
    public function __construct($array = [])
    {
        foreach ($array as $key=>$value) {
            $this->{$key} = $value;
        }
    }
}
