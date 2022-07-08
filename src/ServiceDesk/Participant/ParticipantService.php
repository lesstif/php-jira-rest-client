<?php

declare(strict_types=1);

namespace JiraRestApi\ServiceDesk\Participant;

use JiraRestApi\JiraException;
use JiraRestApi\ServiceDesk\Customer\Customer;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JsonMapper_Exception;
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
     * @throws JiraException|JsonMapper_Exception
     */
    public function getParticipantOfRequest(string $issueIdOrKey, int $start = 0, int $limit = 50): array
    {
        $this->logger->debug("getParticipant=\n");

        $result = $this->client->exec(
            $this->client->createUrl('/request/%s/participant?start=%d&limit=%d', [$issueIdOrKey, $start, $limit])
        );

        $this->logger->debug('get participant result=' . var_export($result, true));

        return $this->mapResult($result);
    }

    /**
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/api-group-request/#api-rest-servicedeskapi-request-issueidorkey-participant-post
     *
     * @param Customer[] $participants
     *
     * @return Customer[] The participants of the customer request.
     * @throws JiraException|JsonMapper_Exception
     */
    public function addParticipantToRequest(string $issueIdOrKey, array $participants): array
    {
        $this->logger->debug("addParticipant=\n");

        $result = $this->client->exec(
            $this->client->createUrl('/request/%s/participant', [$issueIdOrKey]),
            $this->encodeParticipants($participants)
        );

        $this->logger->debug('add participant result=' . var_export($result, true));

        return $this->mapResult($result);
    }

    /**
     * @see https://developer.atlassian.com/cloud/jira/service-desk/rest/api-group-request/#api-rest-servicedeskapi-request-issueidorkey-participant-delete
     *
     * @param Customer[] $participants
     *
     * @return Customer[] The first page of participants of the customer request after removing the specified users.
     *
     * @throws JiraException
     * @throws JsonException|JsonMapper_Exception
     */
    public function removeParticipantFromRequest(string $issueIdOrKey, array $participants): array
    {
        $this->logger->debug("removeParticipant=\n");

        $result = $this->client->exec(
            $this->client->createUrl('/request/%s/participant', [$issueIdOrKey]),
            $this->encodeParticipants($participants),
            'DELETE'
        );

        $this->logger->debug('remove participant result=' . var_export($result, true));

        return $this->mapResult($result);
    }

    /**
     * @param Customer[] $participants
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
     * @return Customer[]
     * @throws JsonMapper_Exception
     */
    private function mapResult(string $result): array
    {
        $userData = json_decode($result, false);

        return array_map(function (object $user): Customer {
            return $this->client->mapWithoutDecode(
                $user,
                new Customer()
            );
        }, $userData->values ?? []);
    }
}
