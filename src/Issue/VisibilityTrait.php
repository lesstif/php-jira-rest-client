<?php

namespace JiraRestApi\Issue;

trait VisibilityTrait
{
    public function setVisibility(Visibility $type): static
    {
        $this->visibility = $type;

        return $this;
    }

    public function setVisibilityAsArray(array $array): static
    {
        if (is_null($this->visibility)) {
            $this->visibility = new Visibility();
        }

        $this->visibility->setType($array['type']);
        $this->visibility->setValue($array['value']);

        return $this;
    }

    public function setVisibilityAsString(string $type, string $value): static
    {
        if (is_null($this->visibility)) {
            $this->visibility = new Visibility();
        }

        $this->visibility->setType($type);
        $this->visibility->setValue($value);

        return $this;
    }
}
