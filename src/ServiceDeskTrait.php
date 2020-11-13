<?php declare(strict_types=1);

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
