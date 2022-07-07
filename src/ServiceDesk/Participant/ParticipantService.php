<?php

declare(strict_types=1);

namespace JiraRestApi\ServiceDesk\Participant;

use JiraRestApi\JiraException;
use JiraRestApi\ServiceDesk\Customer\Customer;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JiraRestApi\User\User;
use Psr\Log\LoggerInterface;

class ParticipantService
{
    /**
     * @var ServiceDeskClient
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ServiceDeskClient $client)
    {
        $this->client = $client;
        $this->logger = $client->getLogger();
    }

    /**
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/api-group-request/#api-rest-servicedeskapi-request-issueidorkey-participant-get
     *
     * @return Customer[] The participants of the customer request, at the specified page of the results.
     * @throws JiraException
     */
    public function getParticipantOfRequest(int $requestId, int $start = 0, int $limit = 50): array
    {
        $this->logger->debug("getParticipant=\n");

        $result = $this->client->exec(
            $this->client->createUrl('/request/%d/participant?start=%d&limit=%d', [$requestId, $start, $limit])
        );

        $this->logger->debug('get participant result=' . var_export($result, true));

        return $this->mapResult($result);
    }

    /**
     * @param User[] $participants
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/api-group-request/#api-rest-servicedeskapi-request-issueidorkey-participant-post
     *
     * @return Customer[] The participants of the customer request.
     * @throws JiraException
     */
    public function addParticipantToRequest(int $requestId, array $participants): array
    {
        $this->logger->debug("addParticipant=\n");

        $result = $this->client->exec(
            $this->client->createUrl('/request/%d/participant', [$requestId]),
            $this->encodeParticipants($participants)
        );

        $this->logger->debug('add participant result=' . var_export($result, true));

        return $this->mapResult($result);
    }

    /**
     * @param Customer[] $participants
     *
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/api-group-request/#api-rest-servicedeskapi-request-issueidorkey-participant-delete
     *
     * @return Customer[] The first page of participants of the customer request after removing the specified users.
     *
     * @throws JiraException
     * @throws JsonException
     */
    public function removeParticipantFromRequest(int $requestId, array $participants): array
    {
        $this->logger->debug("removeParticipant=\n");

        $result = $this->client->exec(
            $this->client->createUrl('/request/%d/participant', [$requestId]),
            $this->encodeParticipants($participants),
            'DELETE'
        );

        $this->logger->debug('remove participant result=' . var_export($result, true));

        return $this->mapResult($result);
    }

    /**
     * @param Customer[] $participants
     *
     * @return string
     */
    private function encodeParticipants(array $participants): string
    {
        return json_encode([
            'accountIds' => array_map(static function (Customer $participant): string {
                return $participant->accountId ?? $participant->emailAddress;
            }, $participants),
        ]);
    }

    /**
     * @param string $result
     *
     * @return Customer[]
     */
    private function mapResult(string $result): array
    {
        $userData = json_decode($result);
        $users = [];

        foreach ($userData->values as $user) {
            $users[] = $this->client->mapWithoutDecode(
                $user,
                new Customer()
            );
        }

        return $users;
    }
}
