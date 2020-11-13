<?php

declare(strict_types=1);

namespace JiraRestApi\Issue;

trait VisibilityTrait
{
    /**
     * @param Visibility|string $type
     * @param null              $value
     *
     * @return $this
     */
    public function setVisibility($type, $value = null)
    {
        if (is_null($this->visibility)) {
            $this->visibility = new Visibility();
        }

        if ($type instanceof Visibility) {
            $this->visibility = $type;
        } elseif (is_string($type)) {
            $this->visibility->setType($type);
            $this->visibility->setValue($value);
        }

        return $this;
    }
}
