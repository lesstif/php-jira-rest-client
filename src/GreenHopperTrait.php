<?php

namespace JiraRestApi;

trait GreenHopperTrait
{
    private function setupAPIUri($version = '1.0')
    {
        $this->setAPIUri('/rest/greenhopper/'.$version);
    }
}
