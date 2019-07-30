<?php

namespace JiraRestApi\Component;

use JiraRestApi\JiraException;

class ComponentService extends \JiraRestApi\JiraClient
{
    private $uri = '/component';

    /**
     * Function to create a new compoonent.
     *
     * @param Component|array $component
     *
     * @throws \JiraRestApi\JiraException
     * @throws \JsonMapper_Exception
     *
     * @return Component class
     */
    public function create($component)
    {
        $data = json_encode($component);

        $this->log->info("Create Component=\n".$data);

        $ret = $this->exec($this->uri, $data, 'POST');

        return $this->json_mapper->map(
            json_decode($ret),
            new Component()
        );
    }

    /**
     * get component.
     *
     * @param $id component id
     *
     * @return Component
     */
    public function get($id)
    {
        $ret = $this->exec($this->uri.'/'.$id);

        $this->log->info('Result='.$ret);

        return $this->json_mapper->map(
            json_decode($ret),
            new Component()
        );
    }

    /**
     * @param Component $component
     *
     * @throws JiraException
     *
     * @return Component
     */
    public function update(Component $component)
    {
        if (!$component->id || !is_numeric($component->id)) {
            throw new JiraException($component->id.' is not a valid component id.');
        }

        $data = json_encode($component);
        $ret = $this->exec($this->uri.'/'.$component->id, $data, 'PUT');

        return $this->json_mapper->map(
            json_decode($ret),
            new Component()
        );
    }

    /**
     * @param Component       $component
     * @param Component|false $moveIssuesTo
     *
     * @throws JiraException
     *
     * @return bool
     */
    public function delete(Component $component, $moveIssuesTo = false)
    {
        if (!$component->id || !is_numeric($component->id)) {
            throw new JiraException($component->id.' is not a valid component id.');
        }

        $data = [];
        $paramArray = [];

        if ($moveIssuesTo && $moveIssuesTo instanceof Component) {
            $paramArray['moveIssuesTo'] = $moveIssuesTo->id;
        }

        $ret = $this->exec($this->uri.'/'.$component->id.$this->toHttpQueryParameter($paramArray), json_encode($data), 'DELETE');

        return $ret;
    }
}
