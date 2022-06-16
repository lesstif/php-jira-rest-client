<?php

namespace JiraRestApi\Issue;

use JiraRestApi\ClassSerialize;

class IssueFieldV3 extends IssueField
{
    use ClassSerialize;

    /** @var \JiraRestApi\Issue\DescriptionV3|null */
    public ?DescriptionV3 $descriptionV3;

    /** @var \JiraRestApi\Issue\DescriptionV3|null */
    public ?DescriptionV3 $environmentV3;

    /**
     * @param \JiraRestApi\Issue\DescriptionV3|null $description
     *
     * @return $this|IssueField
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
}
