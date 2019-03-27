<?php

namespace JiraRestApi\Issue;

use JiraRestApi\ClassSerialize;

class IssueFieldV3 extends IssueField
{
    use ClassSerialize;

    /** @var \JiraRestApi\Issue\DescriptionV3|null */
    public $description;

    /** @var \JiraRestApi\Issue\DescriptionV3|null */
    public $environment;

    public function jsonSerialize()
    {
        $vars = array_filter(get_object_vars($this), function ($var) {
            return !is_null($var);
        });

        // if assignee property has empty value then remove it.
        // @see https://github.com/lesstif/php-jira-rest-client/issues/126
        // @see https://github.com/lesstif/php-jira-rest-client/issues/177
        if (!empty($this->assignee)) {
            // do nothing
            if ($this->assignee->isWantUnassigned() === true) {
            } elseif ($this->assignee->isEmpty()) {
                unset($vars['assignee']);
            }
        }

        // clear undefined json property
        unset($vars['customFields']);

        // repackaging custom field
        if (!empty($this->customFields)) {
            foreach ($this->customFields as $key => $value) {
                $vars[$key] = $value;
            }
        }

        return $vars;
    }

    /**
     * @param \JiraRestApi\Issue\DescriptionV3|null $description
     *
     * @return $this|IssueField
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param \JiraRestApi\Issue\DescriptionV3|null $description
     *
     * @return $this
     */
    public function addDescriptionParagraph($description)
    {
        if (empty($this->description)) {
            $this->description = new DescriptionV3();
        }

        $this->description->addDescriptionContent('paragraph', $description);

        return $this;
    }

    /**
     * @param int    $level       heading level
     * @param string $description
     *
     * @return $this
     */
    public function addDescriptionHeading($level, $description)
    {
        if (empty($this->description)) {
            $this->description = new DescriptionV3();
        }

        $this->description->addDescriptionContent('heading', $description, ['level' => $level]);

        return $this;
    }

    /**
     * @param \JiraRestApi\Issue\DescriptionV3|null $environment
     *
     * @return $this
     */
    public function setEnvironment($environment)
    {
        if (!empty($environment)) {
            $this->environment = $environment;
        }

        return $this;
    }
}
