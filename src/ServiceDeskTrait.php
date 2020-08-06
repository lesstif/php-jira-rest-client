<?php

namespace JiraRestApi;

trait ServiceDeskTrait
{
    private function setupAPIUri($version = '')
    {
        $uri = '/rest/servicedeskapi';
        $uri .= ($version != '') ? '/'.$version : '';
        $this->setAPIUri($uri);
    }
}
