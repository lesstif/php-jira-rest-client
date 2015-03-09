<?php

namespace JiraRestApi\Issue;

class Visibility {
	public $type;
	public $value;
}

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

	/* @var Visibility */
	public $visibility;

	public function setBody($body) {
		$this->body = $body;
       	return $this;
    }

    public function setVisibility($type, $value) {
    	if (is_null($this->visibility))
    		$this->visibility = array();

		$this->visibility['type'] = $type;
		$this->visibility['value'] = $value;
       	return $this;
    }
 
	public function jsonSerialize()
   {
      return array_filter(get_object_vars($this));
   }
}

?>