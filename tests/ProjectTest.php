<?php

use JiraRestApi\Project\ProjectService;

class ProjectTest extends PHPUnit_Framework_TestCase
{
    public function testGetProject()
    {
        $proj = new ProjectService();

        $p = $proj->get('TEST');

        $this->assertTrue($p instanceof JiraRestApi\Project\Project);
        $this->assertTrue(strlen($p->key) > 0);
        $this->assertTrue(!empty($p->id));
        $this->assertTrue(strlen($p->name) > 0);
        // $this->assertTrue(strlen($p->projectCategory['name']) > 0);
    }

    public function testGetProjectLists()
    {
        $proj = new ProjectService();

        $prjs = $proj->getAllProjects();

        foreach ($prjs as $p) {
            $this->assertTrue($p instanceof JiraRestApi\Project\Project);
            $this->assertTrue(strlen($p->key) > 0);
            $this->assertTrue(!empty($p->id));
            $this->assertTrue(strlen($p->name) > 0);
            // $this->assertTrue(strlen($p->projectCategory['name']) > 0);
        }
    }

    public function testGetProjectTypes()
    {
        $proj = new ProjectService();

        $prjtyps = $proj->getProjectTypes();

        foreach ($prjtyps as $pt) {
            $this->assertTrue($pt instanceof JiraRestApi\Project\ProjectType);
            $this->assertTrue(strlen($pt->key) > 0);
            $this->assertTrue(strlen($pt->formattedKey) > 0);
            $this->assertTrue(strlen($pt->descriptionI18nKey) > 0);
            $this->assertTrue(strlen($pt->color) > 0);
            $this->assertTrue(strlen($pt->icon) > 0);
        }
    }

    public function testGetProjectType()
    {
        $proj = new ProjectService();

        $prjtyp = $proj->getProjectType('software');

        $this->assertTrue($prjtyp instanceof JiraRestApi\Project\ProjectType);
        $this->assertTrue(strlen($prjtyp->key) > 0);
        $this->assertTrue(strlen($prjtyp->formattedKey) > 0);
        $this->assertTrue(strlen($prjtyp->descriptionI18nKey) > 0);
        $this->assertTrue(strlen($prjtyp->color) > 0);
        $this->assertTrue(strlen($prjtyp->icon) > 0);
    }

    /**
     * @expectedException JiraRestApi\JiraException
     */
    public function testGetProjectTypeException()
    {
        $proj = new ProjectService();

        $prjtyp = $proj->getProjectType('foobar');
    }

    public function testGetProjectAccessible()
    {
        $proj = new ProjectService();

        $prjtyp = $proj->getAccessibleProjectType('business');

        $this->assertTrue($prjtyp instanceof JiraRestApi\Project\ProjectType);
        $this->assertTrue(strlen($prjtyp->key) > 0);
        $this->assertTrue(strlen($prjtyp->formattedKey) > 0);
        $this->assertTrue(strlen($prjtyp->descriptionI18nKey) > 0);
        $this->assertTrue(strlen($prjtyp->color) > 0);
        $this->assertTrue(strlen($prjtyp->icon) > 0);
    }

    /**
     * @expectedException JiraRestApi\JiraException
     */
    public function testGetProjectAccessibleException()
    {
        $proj = new ProjectService();

        $prjtyp = $proj->getAccessibleProjectType('foobar');
    }

    public function testGetProjectVersion()
    {
        $proj = new ProjectService();

        $prjs = $proj->getVersions('TEST');

        var_dump($prjs);
    }
}
