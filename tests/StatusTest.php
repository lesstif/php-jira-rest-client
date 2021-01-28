<?php

namespace JiraRestApi\Test;

use JiraRestApi\Status\Status;
use PHPUnit\Framework\TestCase;
use JiraRestApi\Status\StatusService;

class StatusTest extends TestCase
{
    public function testStatus()
    {
        $statusService = new StatusService();
        $statuses = $statusService->getAll();
        foreach ($statuses as $s) {
            $this->assertTrue($s instanceof Status);
            $this->assertTrue(!empty($s->name) > 0);
            $this->assertTrue(!empty($s->id));
        }
    }
}
