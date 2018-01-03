<?php

use JiraRestApi\Dumper;
use JiraRestApi\Field\Field;
use JiraRestApi\Field\FieldService;
use JiraRestApi\JiraException;

class CustomFieldsTest extends PHPUnit_Framework_TestCase
{
    public function testGetFields()
    {
        try {
            $fieldService = new FieldService();

            $ret = $fieldService->getAllFields(Field::CUSTOM);
            Dumper::dump($ret);

            $ids = array_map(function($cf) {
                // extract custom field id
                    preg_match('/\d+/', $cf->id, $matches);
                    return $matches[0];
                }, $ret);

            return $ids;

        } catch (JiraException $e) {
            $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
        }
    }

    /**
     * @depends testGetFields
     *
     * @param $ids
     */
    public function testGetFieldOptions($ids)
    {
        try {
            $fieldService = new FieldService();

            foreach ($ids as $id) {
                try {
                    $ret = $fieldService->getCustomFieldOption($id);
                    Dumper::dump($ret);
                }catch (JiraException $e) {}
            }
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testGetFieldOptions Failed : '.$e->getMessage());
        }
    }

    public function testCreateFields()
    {
        $this->markTestSkipped();
        try {
            $field = new Field();

            $field->setName('New custom field')
                ->setDescription('Custom field for picking groups')
                ->setType('com.atlassian.jira.plugin.system.customfieldtypes:grouppicker')
                ->setSearcherKey('com.atlassian.jira.plugin.system.customfieldtypes:grouppickersearcher');

            $fieldService = new FieldService();

            $ret = $fieldService->create($field);
            Dumper::dump($ret);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'Field Create Failed : '.$e->getMessage());
        }
    }
}
