<?php

declare(strict_types=1);

namespace JiraRestApi;

trait AgileApiTrait
{
    private function setupAPIUri($version = '1.0'): void
    {
        $this->setAPIUri('/rest/agile/'.$version);
    }
}
