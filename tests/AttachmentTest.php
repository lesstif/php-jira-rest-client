<?php

use JiraRestApi\Attachment\AttachmentService;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\RemoteIssueLink;
use JiraRestApi\JiraException;

class AttachmentTest extends PHPUnit_Framework_TestCase
{
    public function testGetAttachment()
    {
        $attachmentId = getenv("ID");
        if ($attachmentId == FALSE)
            $attachmentId = 12622;

        try {
            $atts = new AttachmentService();

            $att = $atts->get($attachmentId);

            return $attachmentId;
        } catch (JiraException $e) {
            $this->assertTrue(false, 'Create Failed : '.$e->getMessage());
        }
    }

    /**
     *
     */
    public function testRemoveAttachment()
    {
        $attachmentId = 12622;
        try {
            $atts = new AttachmentService();

            $atts->remove($attachmentId);

            $this->assertGreaterThan(0, count(1));

            //$this->assertInstanceOf(RemoteIssueLink::class, $rils[0]);

           // return $issueKey;
        } catch (HTTPException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }


}
