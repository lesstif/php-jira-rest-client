<?php

namespace JiraRestApi\Issue;

use JiraRestApi\ClassSerialize;

/**
 * Atlassian Document Format.
 *
 * @see https://developer.atlassian.com/cloud/jira/platform/apis/document/structure/
 */
class IssueFieldV3 extends IssueField
{
    use ClassSerialize;

    public ?DescriptionV3 $descriptionV3;

    public ?DescriptionV3 $environmentV3;

    /**
     * @param \JiraRestApi\Issue\DescriptionV3|null $description
     *
     * @return IssueFieldV3
     */
    public function setDescriptionV3(?DescriptionV3 $description): static
    {
        $this->descriptionV3 = $description;

        return $this;
    }

    /**
     * @param \JiraRestApi\Issue\DescriptionV3|null $description
     *
     * @return $this
     */
    public function addDescriptionParagraph(?DescriptionV3 $description): static
    {
        if (empty($this->description)) {
            $this->descriptionV3 = new DescriptionV3();
        }

        $this->descriptionV3->addDescriptionContent('paragraph', $description);

        return $this;
    }

    /**
     * @param int    $level       heading level
     * @param string $description
     *
     * @return $this
     */
    public function addDescriptionHeading($level, string $description): static
    {
        if (empty($this->descriptionV3)) {
            $this->descriptionV3 = new DescriptionV3();
        }

        $this->descriptionV3->addDescriptionContent('heading', $description, ['level' => $level]);

        return $this;
    }

    /**
     * @param \JiraRestApi\Issue\DescriptionV3|null $environment
     *
     * @return $this
     */
    public function setEnvironment(?DescriptionV3 $environment): static
    {
        if (!empty($this->environmentV3)) {
            $this->environmentV3 = $environment;
        }

        return $this;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $params = array_filter(get_object_vars($this), function ($var) {
            return !is_null($var);
        });

        if ($this->descriptionV3 != null) {
            $params['description'] = $this->descriptionV3;
            unset($params['descriptionV3']);
        }

        return $params;
    }
}
