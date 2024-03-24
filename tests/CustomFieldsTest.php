<?php

namespace JiraRestApi\Test;

use JiraRestApi\Issue\IssueService;
use PHPUnit\Framework\TestCase;
use JiraRestApi\Dumper;
use JiraRestApi\Field\Field;
use JiraRestApi\Field\FieldService;
use JiraRestApi\JiraException;

class CustomFieldsTest extends TestCase
{
    /**
     * @Test
     *
     * @return array|string[]|void
     */
    public function get_customer_field()
    {
        try {
            $iss = new IssueService();

            $paramArray = [
                'startAt' => 1,
                'maxResults' => 50,
                'search' => null,
                'projectIds' => [1, 2, 3],
                'screenIds' => null,
                'types' => null,

                'sortOrder' => null,
                'sortColumn' => null,
                'lastValueUpdate' => null,
            ];
            $customerFieldSearchResult = $iss->getCustomFields($paramArray);

            $this->assertLessThan(1, $customerFieldSearchResult->total);

        } catch (JiraException $e) {
            $this->assertTrue(false, 'testSearch Failed : '.$e->getMessage());
        }
    }

    public function testGetFields()
    {
        try {
            $fieldService = new FieldService();

            $ret = $fieldService->getAllFields(Field::CUSTOM);
            Dumper::dump($ret);

            file_put_contents("custom-field.json", json_encode($ret, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));

            $ids = array_map(function($cf) {
                // extract custom field id
                    preg_match('/\d+/', $cf->id, $matches);
                    return $matches[0];
                }, $ret);

            $this->assertTrue(true);
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
            $this->assertTrue(true);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'testGetFieldOptions Failed : '.$e->getMessage());
        }
    }

    public function testCreateFields()
    {
        //$this->markTestSkipped();
        try {
            $field = new Field();

            $field->setName('다중 선택이')
                ->setDescription('Custom field for picking groups')
                ->setType("com.atlassian.jira.plugin.system.customfieldtypes:cascadingselect")
            //    ->setSearcherKey('com.atlassian.jira.plugin.system.customfieldtypes:grouppickersearcher')
            ;

            $fieldService = new FieldService();

            $ret = $fieldService->create($field);

            $this->assertTrue(true);

            Dumper::dump($ret);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'Field Create Failed : '.$e->getMessage());
        }
    }
}
