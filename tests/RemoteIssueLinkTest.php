<?php

use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\RemoteIssueLink;
use JiraRestApi\JiraException;

class RemoteIssueLinkTest extends PHPUnit_Framework_TestCase
{

    public function testCreateRemoteIssueLink()
    {
        $issueKey = 'TEST-316';

        try {
            $issueService = new IssueService();

            $ril = new RemoteIssueLink();

            $ril->setUrl('http://www.mycompany.com/support?id=1')
                ->setTitle('Remote Link Title')
                ->setRelationship('causes')
                ->setSummary('Crazy customer support issue')
            ;

            $issueService->createOrUpdateRemoteIssueLink($issueKey, $ril);

            return $issueKey;
        } catch (JiraException $e) {
            $this->assertTrue(false, 'Create Failed : '.$e->getMessage());
        }
    }

    /**
     * @depends testCreateRemoteIssueLink
     */
    public function testGetRemoteIssue($issueKey)
    {

        try {
            $issueService = new IssueService();

            $rils = $issueService->getRemoteIssueLink($issueKey);

            $this->assertGreaterThan(0, count($rils));

            $this->assertInstanceOf(RemoteIssueLink::class, $rils[0]);

            return $issueKey;
        } catch (HTTPException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }


    /**
     * @depends testGetRemoteIssue
     */
    public function testDeleteRemoteIssueLink($issueKey)
    {
        // not yet impl
        $this->markTestIncomplete();
        try {

            return $issueKey;
        } catch (JiraException $e) {
            $this->assertTrue(false, 'Create Failed : '.$e->getMessage());
        }
    }
}
