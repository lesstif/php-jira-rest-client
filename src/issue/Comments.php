<?php

namespace JiraRestApi\Issue;

class Comment {
	/* @var string */
	public $self;

	/* @var string */
	public $id;

	/* @var Reporter */
	public $author;

	/* @var string */
	public $body;

	/* @var Reporter */
	public $updateAuthor;

	/* @var DateTime */
	public $created;

	/* @var DateTime */
	public $updated;
}

class Comments {	
	/* @var int */
    public $startAt;

    /* @var int */
    public $maxResults;

    /* @var int */
    public $total;

    /* @var CommentList[\JiraRestApi\Issue\Comment] */
    public $comments;
}

?>