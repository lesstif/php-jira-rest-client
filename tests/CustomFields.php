<?php

use JiraRestApi\Dumper;
use JiraRestApi\JiraException;
use JiraRestApi\Field\FieldService;
use JiraRestApi\Field\Field;

class CustomFieldsTest extends PHPUnit_Framework_TestCase
{
    public function testGetFields()
    {
        try {
            $fieldService = new FieldService();

            $ret = $fieldService->getAllFields(Field::CUSTOM);
            Dumper::dump($ret);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
        }
    }

    public function testCreateFields()
    {
        //$this->markTestSkipped();
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
