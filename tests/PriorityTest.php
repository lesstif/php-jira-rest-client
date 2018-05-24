<?php

use JiraRestApi\Dumper;
use JiraRestApi\HTTPException;

use JiraRestApi\Priority\PriorityService;

class PriorityTest extends PHPUnit_Framework_TestCase
{
    public function testAllPriority()
    {
        try {
            $ps = new PriorityService();

            $pl = $ps->getAll();

            $this->assertGreaterThan(1, count($pl));

            var_dump($pl);

            $priority = $pl[count($pl) - 1];

            return $priority;

        } catch (JiraException $e) {
            $this->assertTrue(FALSE, "add Comment Failed : " . $e->getMessage());
        }
    }

    /**
     * @depends testAllPriority
     *
     */
    public function testPriority($priority)
    {
        try {
            $ps = new PriorityService();

            $pl = $ps->get($priority->id);

            $this->assertEquals($priority->id, $pl->id);
            $this->assertEquals($priority->description, $pl->description);

        } catch (JiraException $e) {
            $this->assertTrue(FALSE, "add Comment Failed : " . $e->getMessage());
        }
    }
}
