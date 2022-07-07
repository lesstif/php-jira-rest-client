<?php

declare(strict_types=1);

namespace JiraRestApi\ServiceDesk\Participant;

use JiraRestApi\JiraException;
use JiraRestApi\ServiceDesk\Customer\Customer;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JiraRestApi\User\User;
use JsonMapper_Exception;

class ParticipantService
{
    /**
     * @var ServiceDeskClient
     */
    private $client;

    public function __construct(ServiceDeskClient $client)
    {
        $this->client = $client;
    }

    /**
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/request/{issueIdOrKey}/participant-getRequestParticipants
     *
     * @return Customer[] The participants of the customer request, at the specified page of the results.
     * @throws JiraException
     */
    public function getParticipantOfRequest(int $requestId, int $start = 0, int $limit = 50): array
    {
        $this->client->log("getParticipant=\n");

        $result = $this->client->exec(
            $this->client->createUrl('servicedeskapi/request/%d/participant?start=%d&limit=%d', [$requestId, $start, $limit])
        );

        $this->client->getLogger()->debug('get participant result=' . var_export($result, true));

        return $this->mapResult($result);
    }

    /**
     * @param User[] $participants
     *
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/request/{issueIdOrKey}/participant-addRequestParticipants
     *
     * @return Customer[] The participants of the customer request.
     * @throws JiraException
     */
    public function addParticipantToRequest(int $requestId, array $participants): array
    {
        $this->client->log("addParticipant=\n");

        $result = $this->client->exec(
            $this->client->createUrl('servicedeskapi/request/%d/participant', [$requestId]),
            $this->encodeParticipants($participants)
        );

        $this->client->getLogger()->debug('add participant result=' . var_export($result, true));

        return $this->mapResult($result);
    }

    /**
     * @param Customer[] $participants
     *
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/request/{issueIdOrKey}/participant-removeRequestParticipants
     *
     * @return Customer[] The first page of participants of the customer request after removing the specified users.
     *
     * @throws JiraException
     * @throws JsonException
     */
    public function removeParticipantFromRequest(int $requestId, array $participants): array
    {
        $this->client->log("removeParticipant=\n");

        $result = $this->client->exec(
            $this->client->createUrl('servicedeskapi/request/%d/participant', [$requestId]),
            $this->encodeParticipants($participants),
            'DELETE'
        );

        $this->client->getLogger()->debug('remove participant result=' . var_export($result, true));

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
            'usernames' => array_map(static function (Customer $participant): string {
                return $participant->name;
            }, $participants),
        ]);
    }

    /**
     * @param string $result
     *
     * @return Customer[]
     * @throws JsonMapper_Exception
     */
    private function mapResult(string $result): array
    {
        $userData = json_decode($result);
        $users = [];

        foreach ($userData->values as $user) {
            $users[] = $this->client->map(
                $user,
                new Customer()
            );
        }

        return $users;
    }
}
