<?php

namespace JiraRestApi\Board;

use JiraRestApi\ClassSerialize;


class Board implements \JsonSerializable
{
  use ClassSerialize;

  /* @var string */
  public $id;

  /* @var string */
  public $self;

  /* @var string*/
  public $name;

  /* @var string */
  public $type;

  public function jsonSerialize()
  {
      return array_filter(get_object_vars($this), function ($var) {
          return !is_null($var);
      });
  }
}
