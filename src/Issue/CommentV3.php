<?php

namespace JiraRestApi\Issue;

class CommentV3 extends Comment
{

    /** @var \JiraRestApi\Issue\DescriptionV3|null */
    public $body;

    
    public function setBody($body)
    {
        $this->body = new DescriptionV3();
        $this->body->addDescriptionContent('paragraph', $body);

        return $this;
    }

    
}
