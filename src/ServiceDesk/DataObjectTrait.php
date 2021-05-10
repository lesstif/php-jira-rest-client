<?php

namespace JiraRestApi\ServiceDesk;

trait DataObjectTrait
{
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $setter = $this->getSetterName($key);

            if (method_exists($this, $setter)) {
                $this->$setter($value);
            } elseif (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function jsonSerialize(): array
    {
        return array_filter(get_object_vars($this));
    }

    private function getSetterName(string $key): string
    {
        $key = str_replace('_', '', $key);

        return 'set' . ucfirst($key);
    }
}