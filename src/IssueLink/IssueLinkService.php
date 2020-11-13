<?php declare(strict_types=1);

namespace JiraRestApi\IssueLink;

use JiraRestApi\Exceptions\JiraException;

class IssueLinkService extends \JiraRestApi\JiraClient
{
    private $uri = '';

    /**
     * @param IssueLink $issueLink
     *
     * @throws JiraException
     */
    public function addIssueLink($issueLink)
    {
        $this->log->info("addIssueLink=\n");

        $data = json_encode($issueLink);

        $this->log->debug("Create IssueLink=\n".$data);

        $url = $this->uri.'/issueLink';
        $type = 'POST';

        $this->exec($url, $data, $type);
    }

    /**
     * @throws JiraException
     *
     * @return IssueLinkType[]
     */
    public function getIssueLinkTypes() :array
    {
        $this->log->info("getIssueLinkTYpes=\n");

        $url = $this->uri.'/issueLinkType';

        $ret = $this->exec($url);

        $data = json_encode(json_decode($ret)->issueLinkTypes);

        $linkTypes = $this->json_mapper->mapArray(
            json_decode($data, false),
            new \ArrayObject(),
            '\JiraRestApi\IssueLink\IssueLinkType'
        );

        return $linkTypes;
    }
}
