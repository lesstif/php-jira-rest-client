<?php

namespace JiraRestApi\Test;

use JiraRestApi\Dumper;
use JiraRestApi\Issue\Version;
use JiraRestApi\JiraException;
use JiraRestApi\Project\Project;
use JiraRestApi\Project\ProjectType;
use PHPUnit\Framework\TestCase;
use JiraRestApi\Project\ProjectService;

class ProjectTest extends TestCase
{
    /**
     * @test
     *
     */
    public function get_project_info() : void
    {
        try {
            $proj = new ProjectService();

            $p = $proj->get('TEST');

            $this->assertTrue($p instanceof Project);
            $this->assertTrue(strlen($p->key) > 0);
            $this->assertTrue(!empty($p->id));
            $this->assertTrue(strlen($p->name) > 0);
            // $this->assertTrue(strlen($p->projectCategory['name']) > 0);
        } catch (\Exception $e) {
            $this->fail('get_project_info ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends get_project_info
     *
     */
    public function get_project_lists() : void
    {
        try {
            $proj = new ProjectService();

            $prjs = $proj->getAllProjects();

            foreach ($prjs as $p) {
                $this->assertTrue($p instanceof Project);
                $this->assertTrue(strlen($p->key) > 0);
                $this->assertTrue(!empty($p->id));
                $this->assertTrue(strlen($p->name) > 0);
                // $this->assertTrue(strlen($p->projectCategory['name']) > 0);
            }
        } catch (\Exception $e) {
            $this->fail('get_project_lists ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends get_project_lists
     */
    public function get_project_types() : void
    {
        try {
            $proj = new ProjectService();

            $prjtyps = $proj->getProjectTypes();

            foreach ($prjtyps as $pt) {
                $this->assertTrue($pt instanceof ProjectType);
                $this->assertTrue(strlen($pt->key) > 0);
                $this->assertTrue(strlen($pt->formattedKey) > 0);
                $this->assertTrue(strlen($pt->descriptionI18nKey) > 0);
                $this->assertTrue(strlen($pt->color) > 0);
                $this->assertTrue(strlen($pt->icon) > 0);
            }
        } catch (\Exception $e) {
            $this->fail('get_project_types ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends get_project_types
     *
     */
    public function get_project_type() : void
    {
        try {
            $proj = new ProjectService();

            $prjtyp = $proj->getProjectType('software');

            $this->assertTrue($prjtyp instanceof ProjectType);
            $this->assertTrue(strlen($prjtyp->key) > 0);
            $this->assertTrue(strlen($prjtyp->formattedKey) > 0);
            $this->assertTrue(strlen($prjtyp->descriptionI18nKey) > 0);
            $this->assertTrue(strlen($prjtyp->color) > 0);
            $this->assertTrue(strlen($prjtyp->icon) > 0);
        } catch (\Exception $e) {
            $this->fail('get_project_type ' . $e->getMessage());
        }
    }


    /**
     * @test
     * @depends get_project_types
     *
     */
    public function get_project_accessible() : void
    {
        try {
            $proj = new ProjectService();

            $prjtyp = $proj->getAccessibleProjectType('business');

            $this->assertTrue($prjtyp instanceof ProjectType);
            $this->assertTrue(strlen($prjtyp->key) > 0);
            $this->assertTrue(strlen($prjtyp->formattedKey) > 0);
            $this->assertTrue(strlen($prjtyp->descriptionI18nKey) > 0);
            $this->assertTrue(strlen($prjtyp->color) > 0);
            $this->assertTrue(strlen($prjtyp->icon) > 0);
        } catch (\Exception $e) {
            $this->fail('get_project_accessible ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends get_project_accessible
     */
    public function get_project_version() : void
    {
        try {
            $proj = new ProjectService();

            $prjs = $proj->getVersions('TEST');

            $this->assertNull($prjs);
            $this->assertTrue($prjs instanceof \ArrayObject);
            $this->assertLessThan($prjs->count(), 2);

        } catch (\Exception $e) {
            $this->fail('get_project_version ' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends get_project_accessible
     *
     */
    public function get_unknown_project_type_expect_to_JiraException() : void
    {
        try {
            $proj = new ProjectService();

            $this->expectException(JiraException::class);

            $prjtyp = $proj->getProjectType('foobar');
        } catch (\Exception $e) {
            $this->fail('get_project_type ' . $e->getMessage());
        }
    }
}
