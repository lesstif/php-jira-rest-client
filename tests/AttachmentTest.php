<?php declare(strict_types=1);

use JiraRestApi\Attachment\AttachmentService;
use JiraRestApi\Exceptions\JiraException;
use JiraRestApi\Exceptions\HTTPException;

class AttachmentTest extends \PHPUnit\Framework\TestCase
{
    public function testGetAttachment()
    {
        $attachmentId = 12643;

        try {
            $atts = new AttachmentService();

            $att = $atts->get($attachmentId, "output", true);

            dump($att);

            return $attachmentId;
        } catch (JiraException $e) {
            $this->assertTrue(false, 'Create Failed : '.$e->getMessage());
        }
    }

    /**
     * @depends testGetAttachment
     */
    public function testRemoveAttachment($attachmentId)
    {
        try {
            $atts = new AttachmentService();

            $atts->remove($attachmentId);

            $this->assertGreaterThan(0, count(1));

        } catch (HTTPException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }


}
