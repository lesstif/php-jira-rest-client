<?php

namespace JiraRestApi;

trait AgileApiTrait
{
    private function setupAPIUri($version = '1.0')
    {
        $this->setAPIUri('/rest/agile/'.$version);
    }
}
