<?php

use PHPUnit\Framework\TestCase;
use JiraRestApi\Status\StatusService;

class StatusTest extends TestCase
{
    public function testStatus()
    {
        $statusService = new StatusService();
        $statuses = $statusService->getAll();
        foreach ($statuses as $s) {
            $this->assertTrue($s instanceof JiraRestApi\Status\Status);
            $this->assertTrue(!empty($s->name) > 0);
            $this->assertTrue(!empty($s->id));
        }
    }
}
