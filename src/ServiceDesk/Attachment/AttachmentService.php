<?php

namespace JiraRestApi\ServiceDesk\Attachment;

use JiraRestApi\Issue\Attachment;
use JiraRestApi\JiraException;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JsonMapper_Exception;

class AttachmentService
{
    /**
     * @var ServiceDeskClient
     */
    private $client;

    /**
     * @var int
     */
    private $serviceDeskId;

    public function __construct(ServiceDeskClient $client) {
        $this->client = $client;
        $this->serviceDeskId = $client->getServiceDeskId();
    }

    /**
     * @param Attachment[] $attachments
     * @return string[]
     * @throws JiraException
     */
    public function createTemporaryFiles(array $attachments): array
    {
        $fileNames = $this->getFilenamesFromAttachments($attachments);

        return $this->client->upload(
            $this->client->createUrl('/servicedesk/%d/attachTemporaryFile', [$this->serviceDeskId,]),
            $fileNames
        );
    }

    /**
     * @throws JsonMapper_Exception
     * @throws JiraException
     * @return Attachment[]
     */
    public function addAttachmentToRequest(int $requestId, array $temporaryFiles): array
    {
        $attachment_ids = array_map(function (string $upload): string {
            $upload = json_decode($upload, true);

            return $upload['temporaryAttachments'][0]['temporaryAttachmentId'];
        }, $temporaryFiles);

        $parameters = [
            'temporaryAttachmentIds' => $attachment_ids,
            'public' => false,
        ];

        $result = $this->client->exec(
            $this->client->createUrl('/request/%d/attachment', [$requestId,]),
            json_encode($parameters, JSON_UNESCAPED_UNICODE),
            'POST'
        );

        return $this->createAttachmentsFromJson($result);
    }

    /**
     * @param Attachment[] $attachments
     * @return string[]
     */
    private function getFilenamesFromAttachments(array $attachments): array
    {
        return array_map(
            static function (Attachment $attachment): string {
                return $attachment->filename;
            },
            $attachments
        );
    }

    /**
     * @return Attachment[]
     * @throws JsonMapper_Exception
     */
    private function createAttachmentsFromJson(string $result): array
    {
        $attachmentData = json_decode($result, false);

        $attachments = [];
        foreach($attachmentData as $attachment)
        {
            $attachments[] = $this->client->mapWithoutDecode($attachment, new Attachment());
        }

        return $attachments;
    }
}