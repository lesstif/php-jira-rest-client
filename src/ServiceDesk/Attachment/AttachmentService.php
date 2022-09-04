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

    public function __construct(ServiceDeskClient $client)
    {
        $this->client = $client;
        $this->jsonMapper = $client->getMapper();
    }

    /**
     * @param Attachment[] $attachments
     *
     * @throws JiraException
     *
     * @return string[]
     */
    public function createTemporaryFiles(array $attachments, string $serviceDeskId): array
    {
        $fileNames = $this->getFilenamesFromAttachments($attachments);

        return $this->client->upload(
            $this->client->createUrl('/servicedesk/%s/attachTemporaryFile', [$serviceDeskId]),
            $fileNames
        );
    }

    /**
     * @throws JiraException|JsonException|JsonMapper_Exception
     *
     * @return Attachment[]
     */
    public function addAttachmentToRequest(int $requestId, array $temporaryFiles): array
    {
        $attachment_ids = array_map(static function (string $upload) {
            $upload = json_decode($upload, true, 512, JSON_THROW_ON_ERROR);

            return $upload['temporaryAttachments'][0]['temporaryAttachmentId'];
        }, $temporaryFiles);

        $parameters = [
            'temporaryAttachmentIds' => $attachment_ids,
            'public'                 => false,
        ];

        $result = $this->client->exec(
            $this->client->createUrl('/request/%d/attachment', [$requestId]),
            json_encode($parameters, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            'POST'
        );

        return $this->createAttachmentsFromJson($result);
    }

    /**
     * @param Attachment[] $attachments
     *
     * @return string[]
     */
    private function getFilenamesFromAttachments(array $attachments): array
    {
        return array_map(static function (Attachment $attachment) {
            return $attachment->filename;
        }, $attachments);
    }

    /**
     * @throws JsonMapper_Exception|JsonException
     *
     * @return Attachment[]
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
