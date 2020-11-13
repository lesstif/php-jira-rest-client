<?php declare(strict_types=1);

namespace JiraRestApi;

trait GreenHopperTrait
{
    private function setupAPIUri($version = '1.0')
    {
        $this->setAPIUri('/rest/greenhopper/'.$version);
    }
}
