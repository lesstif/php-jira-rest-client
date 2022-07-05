<?php

declare(strict_types=1);

namespace JiraRestApi\ServiceDesk\Attachment;

use JiraRestApi\Issue\Attachment;
use JiraRestApi\JiraException;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JsonException;
use JsonMapper;
use JsonMapper_Exception;

class AttachmentService
{
    private ServiceDeskClient $client;
    private JsonMapper $jsonMapper;
    private int $serviceDeskId;

    public function __construct(ServiceDeskClient $client)
    {
        $this->client = $client;
        $this->jsonMapper = $client->getMapper();
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
     * @return Attachment[]
     * @throws JiraException|JsonException|JsonMapper_Exception
     */
    public function addAttachmentToRequest(int $requestId, array $temporaryFiles): array
    {
        $attachment_ids = array_map(static function (string $upload): string {
            $upload = json_decode($upload, true, 512, JSON_THROW_ON_ERROR);

            return $upload['temporaryAttachments'][0]['temporaryAttachmentId'];
        }, $temporaryFiles);

        $parameters = [
            'temporaryAttachmentIds' => $attachment_ids,
            'public' => false,
        ];

        $result = $this->client->exec(
            $this->client->createUrl('/request/%d/attachment', [$requestId,]),
            json_encode($parameters, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
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
     * @throws JsonMapper_Exception|JsonException
     */
    private function createAttachmentsFromJson(string $result): array
    {
        $attachmentData = json_decode($result, false, 512, JSON_THROW_ON_ERROR);

        $attachments = [];
        foreach ($attachmentData as $attachment) {
            $attachments[] = $this->jsonMapper->map($attachment, new Attachment());
        }

        return $attachments;
    }
}