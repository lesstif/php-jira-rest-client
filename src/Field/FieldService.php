<?php declare(strict_types=1);

namespace JiraRestApi\Field;

use JiraRestApi\Exceptions\JiraException;
use JiraRestApi\Issue\IssueService;

class FieldService extends \JiraRestApi\JiraClient
{
    private $uri = '/field';

    /**
     * get all field list.
     *
     * @throws JiraException
     *
     * @return Field[] array of Filed class
     */
    public function getAllFields($fieldType = Field::BOTH) :array
    {
        $ret = $this->exec($this->uri, null);

        $fields = $this->json_mapper->mapArray(
            json_decode($ret, false),
            new \ArrayObject(),
            \JiraRestApi\Field\Field::class
        );

        // temp array
        $ar = [];
        if ($fieldType === Field::CUSTOM) {
            foreach ($fields as $f) {
                if ($f->custom === true) {
                    array_push($ar, $f);
                }
            }
            $fields = &$ar;
        } elseif ($fieldType === Field::SYSTEM) {
            foreach ($fields as $f) {
                if ($f->custom === false) {
                    array_push($ar, $f);
                }
            }
            $fields = &$ar;
        }

        return $fields;
    }

    /**
     * Returned if the Custom Field Option exists and is visible by the calling user.
     *
     * Currently, JIRA doesn't provide a method to retrieve custom field's option. instead use getEditMeta().
     *
     * @see IssueService::getEditMeta() .
     *
     * @param string $id custom field option id
     *
     * @throws JiraException
     *
     * @return string
     */
    public function getCustomFieldOption($id) :string
    {
        $ret = $this->exec('/customFieldOption/'.$id);

        $this->log->debug("get custom Field Option=\n".$ret);

        return $ret;
    }

    /**
     * create new field.
     *
     * @param Field $field object of Field class
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Field created field class
     */
    public function create(Field $field) :Field
    {
        $data = json_encode($field);

        $this->log->info("Create Field=\n".$data);

        $ret = $this->exec($this->uri, $data, 'POST');

        $cf = $this->json_mapper->map(
            json_decode($ret),
            new Field()
        );

        return $cf;
    }
}
