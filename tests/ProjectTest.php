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
    public function get_project_lists() : string
    {
        $projKey = 'TEST';

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

        return $projKey;
    }

    /**
     * @test
     * @depends get_project_lists
     */
    public function get_project_info(string $projKey) : string
    {
        try {
            $proj = new ProjectService();

            $p = $proj->get($projKey);

            $this->assertTrue($p instanceof Project);
            $this->assertTrue(strlen($p->key) > 0);
            $this->assertTrue(!empty($p->id));
            $this->assertTrue(strlen($p->name) > 0);
            // $this->assertTrue(strlen($p->projectCategory['name']) > 0);
        } catch (\Exception $e) {
            $this->fail('get_project_info ' . $e->getMessage());
        }

        return $projKey;
    }

    /**
     * @test
     * @depends get_project_info
     */
    public function get_project_types(string $projKey) : string
    {
        try {
            $proj = new ProjectService();

            $projectTypes = $proj->getProjectTypes();

            foreach ($projectTypes as $pt) {
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

        return $projKey;
    }

    /**
     * @test
     * @depends get_project_types
     *
     */
    public function get_software_project_types_only(string $projKey) : string
    {
        try {
            $proj = new ProjectService();

            $projectType = $proj->getProjectType('software');

            $this->assertTrue($projectType instanceof ProjectType);
            $this->assertTrue(strlen($projectType->key) > 0);
            $this->assertTrue(strlen($projectType->formattedKey) > 0);
            $this->assertTrue(strlen($projectType->descriptionI18nKey) > 0);
            $this->assertTrue(strlen($projectType->color) > 0);
            $this->assertTrue(strlen($projectType->icon) > 0);
        } catch (\Exception $e) {
            $this->fail('get_project_type ' . $e->getMessage());
        }

        return $projKey;
    }

    /**
     * @test
     * @depends get_software_project_types_only
     *
     */
    public function get_project_accessible(string $projKey) : string
    {
        try {
            $proj = new ProjectService();

            $projectType = $proj->getAccessibleProjectType('business');

            $this->assertTrue($projectType instanceof ProjectType);
            $this->assertTrue(strlen($projectType->key) > 0);
            $this->assertTrue(strlen($projectType->formattedKey) > 0);
            $this->assertTrue(strlen($projectType->descriptionI18nKey) > 0);
            $this->assertTrue(strlen($projectType->color) > 0);
            $this->assertTrue(strlen($projectType->icon) > 0);
        } catch (\Exception $e) {
            $this->fail('get_project_accessible ' . $e->getMessage());
        }

        return $projKey;
    }

    /**
     * @test
     * @depends get_project_accessible
     */
    public function get_project_version(string $projKey) : string
    {
        try {
            $proj = new ProjectService();

            $prjs = $proj->getVersions('TEST');

            $this->assertIsObject($prjs);
            $this->assertTrue($prjs instanceof \ArrayObject);
            $this->assertLessThan($prjs->count(), 2);

        } catch (\Exception $e) {
            $this->fail('get_project_version ' . $e->getMessage());
        }

        return $projKey;
    }

    /**
     * @test
     * @depends get_project_version
     */
    public function get_project_assignable(string $projKey) : string
    {
        try {
            $proj = new ProjectService();

            $reporters = $proj->getAssignable($projKey);

            $this->assertIsArray($reporters);
            //$this->assertTrue($reporters instanceof \ArrayObject);
            $this->assertLessThan(count($reporters), 2);

        } catch (\Exception $e) {
            $this->fail('get_project_version ' . $e->getMessage());
        }

        return $projKey;
    }

    /**
     * @test
     * @depends get_project_assignable
     *
     */
    public function get_unknown_project_type_expect_to_JiraException(string $projKey) : string
    {
        try {
            $proj = new ProjectService();

            $this->expectException(JiraException::class);

            $projectType = $proj->getProjectType('foobar');
        } catch (\Exception $e) {
            $this->fail('get_project_type ' . $e->getMessage());
        }

        return $projKey;
    }
}
