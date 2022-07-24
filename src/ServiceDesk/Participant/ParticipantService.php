<?php

declare(strict_types=1);

namespace JiraRestApi\ServiceDesk\Participant;

use JiraRestApi\JiraException;
use JiraRestApi\ServiceDesk\Customer\Customer;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JsonException;
use JsonMapper;
use JsonMapper_Exception;
use Psr\Log\LoggerInterface;

class ParticipantService
{
    private ServiceDeskClient $client;
    private LoggerInterface $logger;
    private JsonMapper $mapper;

    public function __construct(ServiceDeskClient $client)
    {
        $this->client = $client;
        $this->logger = $client->getLogger();
        $this->mapper = $client->getMapper();
    }

    /**
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/api-group-request/#api-rest-servicedeskapi-request-issueidorkey-participant-get
     *
     * @throws JiraException|JsonMapper_Exception|JsonException
     *
     * @return Customer[] The participants of the customer request, at the specified page of the results.
     */
    public function getParticipantOfRequest(string $issueIdOrKey, int $start = 0, int $limit = 50): array
    {
        $this->logger->debug("getParticipant=\n");

        $result = $this->client->exec(
            $this->client->createUrl('/request/%s/participant?start=%d&limit=%d', [$issueIdOrKey, $start, $limit])
        );

        $this->logger->debug('get participant result='.var_export($result, true));

        return $this->mapResult($result);
    }

    /**
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/api-group-request/#api-rest-servicedeskapi-request-issueidorkey-participant-post
     *
     * @param Customer[] $participants
     *
     * @throws JiraException|JsonMapper_Exception|JsonException
     *
     * @return Customer[] The participants of the customer request.
     */
    public function addParticipantToRequest(string $issueIdOrKey, array $participants): array
    {
        $this->logger->debug("addParticipant=\n");

        $result = $this->client->exec(
            $this->client->createUrl('/request/%s/participant', [$issueIdOrKey]),
            $this->encodeParticipants($participants)
        );

        $this->logger->debug('add participant result='.var_export($result, true));

        return $this->mapResult($result);
    }

    /**
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/api-group-request/#api-rest-servicedeskapi-request-issueidorkey-participant-delete
     *
     * @param Customer[] $participants
     *
     * @throws JiraException|JsonException|JsonMapper_Exception
     *
     * @return Customer[] The first page of participants of the customer request after removing the specified users.
     */
    public function removeParticipantFromRequest(string $issueIdOrKey, array $participants): array
    {
        $this->logger->debug("removeParticipant=\n");

        $result = $this->client->exec(
            $this->client->createUrl('/request/%s/participant', [$issueIdOrKey]),
            $this->encodeParticipants($participants),
            'DELETE'
        );

        $this->logger->debug('remove participant result='.var_export($result, true));

        return $this->mapResult($result);
    }

    /**
     * @param Customer[] $participants
     *
     * @throws JsonException
     */
    private function encodeParticipants(array $participants): string
    {
        return json_encode([
            'accountIds' => array_map(static function (Customer $participant): string {
                return $participant->accountId ?? $participant->emailAddress;
            }, $participants),
        ], JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonMapper_Exception|JsonException
     *
     * @return Customer[]
     */
    private function mapResult(string $result): array
    {
        $userData = json_decode($result, false, 512, JSON_THROW_ON_ERROR);

        return array_map(function (object $user): Customer {
            return $this->mapper->map(
                $user,
                new Customer()
            );
        }, $userData->values ?? []);
    }
}
