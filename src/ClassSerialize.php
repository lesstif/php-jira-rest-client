<?php

namespace JiraRestApi;

trait ClassSerialize
{
    /**
     * class property to Array.
     *
     * @param array $ignoreProperties this properties to be (ex|in)cluded from array whether second param is true or false.
     * @param bool  $excludeMode
     *
     * @return array
     */
    public function toArray($ignoreProperties = [], $excludeMode = true)
    {
        $tmp = (get_object_vars($this));
        $retAr = null;

        foreach ($tmp as $key => $value) {
            if ($excludeMode === true) {
                if (!in_array($key, $ignoreProperties)) {
                    $retAr[$key] = $value;
                }
            } else {    // include mode
                if (in_array($key, $ignoreProperties)) {
                    $retAr[$key] = $value;
                }
            }
        }

        return $retAr;
    }

    /**
     * class property to String.
     *
     * @param array $ignoreProperties this properties to be excluded from String.
     * @param bool  $excludeMode
     *
     * @return string
     */
    public function toString($ignoreProperties = [], $excludeMode = true)
    {
        $ar = $this->toArray($ignoreProperties, $excludeMode);

        return json_encode($ar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
