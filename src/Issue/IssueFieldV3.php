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
     * @return $this|IssueFieldV3
     */
    public function set_DescriptionV3($description): IssueFieldV3
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @param \JiraRestApi\Issue\DescriptionV3|string $description
     *
     * @return $this
     */
    public function add_DescriptionParagraph($description): IssueFieldV3
    {
        if (empty($this->description)) {
            $this->description = new DescriptionV3();
        }

        if (is_string($description)) {
            $this->description->addDescriptionContent('paragraph', $description);
        } else {
            $this->description = $description;
        }

        return $this;
    }

    /**
     * @param int    $level       heading level
     * @param string $description
     *
     * @return $this
     */
    public function add_DescriptionHeading(int $level, string $description): IssueFieldV3
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
    public function set_Environment(?DescriptionV3 $environment): IssueFieldV3
    {
        if (!empty($environment)) {
            $this->environment = $environment;
        }

        return $this;
    }
}
