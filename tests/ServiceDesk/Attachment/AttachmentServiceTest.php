<?php

declare(strict_types=1);

namespace JiraRestApi\Test\ServiceDesk\Attachment;

use JiraRestApi\Issue\Attachment;
use JiraRestApi\ServiceDesk\Attachment\AttachmentService;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JsonMapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AttachmentServiceTest extends TestCase
{
    public function testCreateTemporaryFiles(): void
    {
        $client = $this->createClient();
        $attachments = [
            $this->createAttachment('image_1.png'),
            $this->createAttachment('image_2.png'),
            $this->createAttachment('image_3.png'),
        ];

        $expected = [
            'temporaryAttachments' => [
                ['temporaryAttachmentId' => '83rhfdkshgd213213'],
                ['temporaryAttachmentId' => '3j4erljfdsn34123d'],
                ['temporaryAttachmentId' => 'dsldwejnhrewrwer21'],
            ],
        ];

        $url = 'https://example.com/upload';
        $serviceDeskId = '15';

        $client->method('createUrl')
            ->with('/servicedesk/%d/attachTemporaryFile', [$serviceDeskId])
            ->willReturn($url);
        $client->method('upload')
            ->with($url, ['image_1.png', 'image_2.png', 'image_3.png'])
            ->willReturn($expected);

        $uut = new AttachmentService($client);

        $result = $uut->createTemporaryFiles($attachments, $serviceDeskId);

        self::assertSame($expected, $result);
    }

    public function testAddAttachmentToRequest(): void
    {
        $temporaryFiles = [
            json_encode(['temporaryAttachments' => [['temporaryAttachmentId' => '83rhfdkshgd213213']]]),
            json_encode(['temporaryAttachments' => [['temporaryAttachmentId' => '3j4erljfdsn34123d']]]),
            json_encode(['temporaryAttachments' => [['temporaryAttachmentId' => 'dsldwejnhrewrwer21']]]),
        ];

        $attachments = [
            ['id' => 10, 'filename' => 'http://example.com/uploads/image_1.png'],
            ['id' => 23, 'filename' => 'http://example.com/uploads/image_2.png'],
            ['id' => 65, 'filename' => 'http://example.com/uploads/image_3.png'],
        ];

        $url = 'https://example.com/upload';

        $client = $this->createClient();
        $client->method('createUrl')
            ->with('/request/%d/attachment', [52])
            ->willReturn($url);
        $client->method('exec')
            ->with(
                $url,
                '{"temporaryAttachmentIds":["83rhfdkshgd213213","3j4erljfdsn34123d","dsldwejnhrewrwer21"],"public":false}',
                'POST'
            )
            ->willReturn(json_encode($attachments));

        $uut = new AttachmentService($client);

        $result = $uut->addAttachmentToRequest(52, $temporaryFiles);

        self::assertCount(3, $result);
        self::assertSame($attachments[0]['id'], $result[0]->id);
        self::assertSame($attachments[0]['filename'], $result[0]->filename);
        self::assertSame($attachments[1]['id'], $result[1]->id);
        self::assertSame($attachments[1]['filename'], $result[1]->filename);
        self::assertSame($attachments[2]['id'], $result[2]->id);
        self::assertSame($attachments[2]['filename'], $result[2]->filename);
    }

    /**
     * @return ServiceDeskClient|MockObject
     */
    private function createClient(): MockObject|ServiceDeskClient
    {
        $mapper = new JsonMapper();

        $client = $this->createMock(ServiceDeskClient::class);
        $client->method('getMapper')->willReturn($mapper);

        return $client;
    }

    private function createAttachment(string $fileName): Attachment
    {
        $attachment = new Attachment();
        $attachment->filename = $fileName;

        return $attachment;
    }
}
