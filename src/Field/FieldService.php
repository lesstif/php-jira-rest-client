<?php

namespace JiraRestApi\Field;

class FieldService extends \JiraRestApi\JiraClient
{
    private $uri = '/field';

    /**
     * get all field list.
     *
     * @return array of Filed class
     */
    public function getAllFields($fieldType = Field::BOTH)
    {
        $ret = $this->exec($this->uri, null);

        $fields = $this->json_mapper->mapArray(
             json_decode($ret, false), new \ArrayObject(), '\JiraRestApi\Field\Field'
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

    public function getCustomFieldOption($id)
    {
        $ret = $this->exec('/customFieldOption', null);

        $this->log->addDebug("Create Field=\n" . $ret);

        return $ret;
    }

    /**
     * create new field.
     *
     * @param   $field object of Field class
     *
     * @return created field class
     */
    public function create(Field $field)
    {
        $data = json_encode($field);

        $this->log->addInfo("Create Field=\n" . $data);

        $ret = $this->exec($this->uri, $data, 'POST');

        $cf = $this->json_mapper->map(
            json_decode($ret), new Field()
        );

        return $cf;
    }
}
