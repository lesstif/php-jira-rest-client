<?php

namespace JiraRestApi\Issue;

trait VisibilityTrait
{
    /**
     * @param Visibility $type
     * @param mixed|null $value
     *
     * @return $this
     */
    public function setVisibility($type, $value = null)
    {
        if (is_null($this->visibility)) {
            $this->visibility = new Visibility();
        }

        if (is_array($type)) {
            $this->visibility->setType($type['type']);
            $this->visibility->setValue($type['value']);
        } elseif ($type instanceof Visibility) {
            $this->visibility = $type;
        } else { // @phpstan-ignore-line We cannot fix phpdoc to make this "reachable" because of JsonMapper
            $this->visibility->setType($type);
            $this->visibility->setValue($value);
        }

        return $this;
    }
}
