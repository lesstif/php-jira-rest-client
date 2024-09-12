<?php

namespace JiraRestApi\Issue;

class CommentV3 extends Comment
{
    /** @var \JiraRestApi\Issue\DescriptionV3|null */
    public $body;

    public function setBody($body)
    {
        return $this->addCommentParagraph($body);
    }

    /**
     * @param \JiraRestApi\Issue\DescriptionV3|null $description
     *
     * @return $this
     */
    public function addCommentParagraph($description)
    {
        if (empty($this->body)) {
            $this->body = new DescriptionV3();
        }

        $this->body->addDescriptionContent('paragraph', $description);

        return $this;
    }
}
