<?php

namespace JiraRestApi\IssueLink;

use JiraRestApi\JiraException;

class IssueLinkService extends \JiraRestApi\JiraClient
{
    private $uri = '';

    public function addIssueLink($issueLink)
    {
        $this->log->addInfo("addIssueLink=\n");

        $data = json_encode($issueLink);

        $this->log->addDebug("Create IssueLink=\n".$data);

        $url = $this->uri . "/issueLink";
        $type = 'POST';

        $ret = $this->exec($url, $data, $type);

        $this->log->addDebug('add issue link result='.var_export($ret, true));
        //$comment = $this->json_mapper->map(
        //    json_decode($ret), new Comment()
        //);

        return $this->http_response === 201 ? true : 'qqq';
        //https://docs.atlassian.com/jira/REST/server/#api/2/issueLink-linkIssues
    }

    public function getIssueLinkTypes()
    {
        $this->log->addInfo("getIssueLinkTYpes=\n");

        $url = $this->uri . "/issueLinkType";

        $ret = $this->exec($url);

        $linkTypes = $this->json_mapper->mapArray(
            json_decode($ret), new \ArrayObject(), '\JiraRestApi\IssueLink\IssueLinkType'
        );

        return $linkTypes;
    }
}
