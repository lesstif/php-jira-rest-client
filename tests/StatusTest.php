<?php declare(strict_types=1);

use JiraRestApi\Status\StatusService;

class StatusTest extends \PHPUnit\Framework\TestCase
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
