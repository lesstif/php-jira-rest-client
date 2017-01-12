<?php

use JiraRestApi\Dumper;
use JiraRestApi\Project\ProjectService;

class ProjectTest extends PHPUnit_Framework_TestCase
{
    public function testGetProject()
    {
        //$this->markTestIncomplete();
        try {
            $proj = new ProjectService();

            $p = $proj->get('TEST');

            Dumper::dump($p);
            foreach ($p->components as $c) {
                echo 'COM : '.$c->name."\n";
            }
        } catch (HTTPException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testGetProjectLists()
    {
        //$this->markTestIncomplete();
        try {
            $proj = new ProjectService();

            $prjs = $proj->getAllProjects();

            foreach ($prjs as $p) {
                echo sprintf("Project Key:%s, Id:%s, Name:%s, projectCategory: %s\n",
                    $p->key, $p->id, $p->name, $p->projectCategory['name']
                    );
            }
        } catch (HTTPException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }
    //

    public function testGetProjectTypes() {
        try {
            $proj = new ProjectService();

            $prjtyps = $proj->getProjectTypes();

            foreach ($prjtyps as $pt) {
				$this->assertTrue($pt instanceof JiraRestApi\Project\ProjectType);
                echo sprintf("ProjectType Key:%s, FormattedKey:%s, descriptionI18nKey:%s, color:%s, icon: %s\n",
                    $pt->key, $pt->formattedKey, $pt->descriptionI18nKey, $pt->color, $pt->icon
                    );
            }
        } catch (HTTPException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testGetProjectType()
    {
        try {
            $proj = new ProjectService();

            $prjtyp = $proj->getProjectType('software');

			$this->assertTrue($prjtyp instanceof JiraRestApi\Project\ProjectType);
			echo sprintf("ProjectType Key:%s, FormattedKey:%s, descriptionI18nKey:%s, color:%s, icon: %s\n",
				$prjtyp->key, $prjtyp->formattedKey, $prjtyp->descriptionI18nKey, $prjtyp->color, $prjtyp->icon
				);
        } catch (HTTPException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    public function testGetProjectAccessible()
    {
        try {
            $proj = new ProjectService();

            $prjtyp = $proj->getProjectType('software');

			$this->assertTrue($prjtyp instanceof JiraRestApi\Project\ProjectType);
			echo sprintf("ProjectType Key:%s, FormattedKey:%s, descriptionI18nKey:%s, color:%s, icon: %s\n",
				$prjtyp->key, $prjtyp->formattedKey, $prjtyp->descriptionI18nKey, $prjtyp->color, $prjtyp->icon
				);
        } catch (HTTPException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }
}
