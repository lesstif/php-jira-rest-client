<?php

namespace JiraRestApi;

trait ClassSerialize
{
    /**
     * class property to Array.
     *
     * @param array $ignoreProperties this properties to be excluded from array.
     *
     * @return array
     */
    public function toArray($ignoreProperties = [])
    {
        $ar = (get_object_vars($this));
        foreach ($ar as $key => $value) {
            if (in_array($key, $ignoreProperties)) {
                unset($ar[$key]);
            }
        }

        return $ar;
    }

    /**
     * class property to String.
     *
     * @param array $ignoreProperties this properties to be excluded from String.
     *
     * @return string
     */
    public function toString($ignoreProperties = [])
    {
        $ar = $this->toArray($ignoreProperties);

        return json_encode($ar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
