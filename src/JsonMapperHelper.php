<?php

namespace JiraRestApi;

class JsonMapperHelper
{
    /**
     * Handle undefined properties during JsonMapper::map().
     *
     * @param object $object    Object that is being filled
     * @param string $propName  Name of the unknown JSON property
     * @param mixed  $jsonValue JSON value of the property
     *
     * @return void
     */
    public static function setUndefinedProperty($object, $propName, $jsonValue)
    {
        // If the property is a custom field type, assign a value to the custom Fields array.
        if (substr($propName, 0, 12) == 'customfield_') {
            if (!empty($jsonValue)) {
                $object->{$propName} = $jsonValue;
                $object->customFields[$propName] = $jsonValue;
            }
        } else {
            $object->{$propName} = $jsonValue;
        }
    }
}
