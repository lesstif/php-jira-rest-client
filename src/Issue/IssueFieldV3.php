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
