<?php

namespace JiraRestApi\Test;

use JiraRestApi\AssigneeTypeEnum;
use JiraRestApi\Component\Component;
use JiraRestApi\Component\ComponentService;
use PHPUnit\Framework\TestCase;

/**
 *
 */
class ComponentTest extends TestCase
{
    /**
     * @test
     */
    public function create_component_on_project() : string
    {
        try {
            $cs = new ComponentService();

            $comp = new Component();
            $name = '내 컴포넌트' . mt_rand(1, 9999);

            $comp->setProjectKey('TEST')
                ->setName($name)
                ->setLeadUserName('lesstif')
                ->setAssigneeTypeAsEnum(AssigneeTypeEnum::PROJECT_LEAD)
                ->setDescription('describe your component here');

            $c = $cs->create($comp);

            $this->assertNotNull($c->id);
            $this->assertNotNull($c->project);

            return $c->id;
        } catch (\Exception $e) {
            $this->fail('create_component_on_project failed' . $e->getMessage());
        }
    }

    /**
     * @test
     * @depends create_component_on_project
     * @param string $component_id
     * @return void
     */
    public function get_component_on_project(string $component_id): void
    {
        try {
            $cs = new ComponentService();

            $c = $cs->get($component_id);

            $this->assertNotNull($c->id);
            $this->assertNotNull($c->project);
        } catch (\Exception $e) {
            $this->fail('get_component_on_project failed' . $e->getMessage());
        }
    }

}
