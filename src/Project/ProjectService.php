<?php

namespace JiraRestApi\Project;

use JiraRestApi\JiraClient;

class ProjectService extends JiraClient
{
    private $uri = '/project';
    private $expand = 'id,name,key,url,description,email';

    /**
     * get all project list.
     * @return bool|mixed
     */
    public function getAllProjects()
    {
        $result = $this->exec($this->uri, ['expand' => $this->expand]);

        return $this->extractErrors($result, [200], function () use ($result) {
            return $this->json_mapper->mapArray(
                $result->getRawData(), new \ArrayObject(), '\JiraRestApi\Project\Project'
            );
        });
    }

    /**
     * @param $projectIdOrKey
     *
     * @return bool|object
     * @throws \JsonMapper_Exception
     */
    public function get($projectIdOrKey)
    {
        $result = $this->exec($this->uri . '/' . $projectIdOrKey);

        return $this->extractErrors($result, [200], function () use ($result) {
            return $this->json_mapper->map(
                $result->getRawData(), new Project()
            );
        });
    }

    /**
     * @param null $expand
     *
     * @return $this
     */
    public function setExpand($expand = null)
    {
        $this->expand = $expand;
        return $this;
    }
}

