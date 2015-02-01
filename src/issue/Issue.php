<?php

namespace JiraRestApi\Issue;

class Issue {	
    /**
     * return only if Project query by key(not id)
     * @var string
     */
    public $expand;

	/* @var string */
    public $self;

    /* @var string */
    public $id;

   /* @var string */
   public $key;

   /* @var IssueField */
   public $fields;

}

?>