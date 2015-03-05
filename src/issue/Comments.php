<?php

namespace JiraRestApi\Issue;

class Comment implements \JsonSerializable {
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

	public function jsonSerialize()
   {
      return array_filter(get_object_vars($this));
   }
}

class Comments implements \JsonSerializable {	
	/* @var int */
    public $startAt;

    /* @var int */
    public $maxResults;

    /* @var int */
    public $total;

    /* @var CommentList[\JiraRestApi\Issue\Comment] */
    public $comments;

   public function jsonSerialize()
   {
      return array_filter(get_object_vars($this));
   }
}

?>