<?php

namespace JiraRestApi\Issue;

/**
 * REST API V3 Issue description field.
 *
 * Class DescriptionV3
 */
class DescriptionV3 implements \JsonSerializable
{
    /* @var string */
    public $self;

    /* @var string */
    public $type = 'doc';

    /* @var integer */
    public $version = 1;

    /** @var \JiraRestApi\Issue\ContentField[]|null */
    public $content;

    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function addDescriptionContent($type, $text, $attrs = [])
    {
        $cf = new ContentField();

        $cf->type = $type;

        if (!empty($attrs)) {
            $cf->attrs = $attrs;
        }

        $cf->content[] = [
            'type' => 'text',
            'text' => $text,
        ];

        if (empty($this->content)) {
            $this->content = [];
        }

        $this->content[] = $cf;
    }
}
