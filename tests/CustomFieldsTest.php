<?php declare(strict_types=1);

use JiraRestApi\Dumper;
use JiraRestApi\Field\Field;
use JiraRestApi\Field\FieldService;
use JiraRestApi\Exceptions\JiraException;
use JiraRestApi\Exceptions\HTTPException;

class CustomFieldsTest extends \PHPUnit\Framework\TestCase
{
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
        //$this->markTestSkipped();
        try {
            $field = new Field();

            $field->set_Name('다중 선택이')
                ->set_Description('Custom field for picking groups')
                ->set_Type("com.atlassian.jira.plugin.system.customfieldtypes:cascadingselect")
            //    ->setSearcherKey('com.atlassian.jira.plugin.system.customfieldtypes:grouppickersearcher')
            ;

            $fieldService = new FieldService();

            $ret = $fieldService->create($field);
            Dumper::dump($ret);
        } catch (JiraException $e) {
            $this->assertTrue(false, 'Field Create Failed : '.$e->getMessage());
        }
    }
}
