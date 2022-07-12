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

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter(get_object_vars($this));
    }

    public function addDescriptionContent($type, $text = null, $attrs = [])
    {
        $cf = new ContentField();

        $cf->type = $type;

        if (!empty($attrs)) {
            $cf->attrs = $attrs;
        }

        if (!empty($text)) {
            $cf->content[] = [
                'type' => 'text',
                'text' => $text,
            ];
        }

        if (empty($this->content)) {
            $this->content = [];
        }

        $this->content[] = $cf;
    }
}
