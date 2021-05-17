<?php

namespace JiraRestApi\ServiceDesk\Request;

use ArrayObject;
use InvalidArgumentException;
use JiraRestApi\Project\ProjectService;
use JiraRestApi\ServiceDesk\Attachment\AttachmentService;
use JiraRestApi\ServiceDesk\Customer\Customer;
use JiraRestApi\ServiceDesk\Comment\CommentService;
use JiraRestApi\ServiceDesk\Comment\Comment;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JsonMapper_Exception;
use JiraRestApi\Issue\Attachment;
use JiraRestApi\Issue\Issue;
use JiraRestApi\Issue\Notify;
use JiraRestApi\Issue\PaginatedWorklog;
use JiraRestApi\Issue\Priority;
use JiraRestApi\Issue\RemoteIssueLink;
use JiraRestApi\Issue\Reporter;
use JiraRestApi\Issue\SecurityScheme;
use JiraRestApi\Issue\TimeTracking;
use JiraRestApi\Issue\Transition;
use JiraRestApi\Issue\Worklog;
use JiraRestApi\JiraException;

class RequestService
{
    /**
     * @var ServiceDeskClient
     */
    private $client;

    /**
     * @var CommentService
     */
    private $commentService;

    /**
     * @var AttachmentService
     */
    private $attachmentService;

    /**
     * @var string
     */
    private $uri = '/request';

    /**
     * @var int
     */
    private $serviceDeskId;

    public function __construct(
        ServiceDeskClient $client,
        CommentService $commentService,
        AttachmentService $attachmentService
    )
    {
        $this->client = $client;
        $this->commentService = $commentService;
        $this->attachmentService = $attachmentService;
        $this->serviceDeskId = $client->getServiceDeskId();
    }

    /**
     * @throws JsonMapper_Exception
     */
    public function getRequestFromJSON(object $jsonData): Request
    {
        return $this->client->mapWithoutDecode($jsonData, new Request());
    }

    /**
     * @throws JiraException
     * @throws JsonMapper_Exception
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/request-getCustomerRequestByIdOrKey
     */
    public function get(int $issueId, array $expandParameters = [], Request $request = null): Request
    {
        $request = ($request) ?: new Request();

        $result = $this->client->exec(
            $this->client->createUrl('%s/%s?%s', [$this->uri, $issueId,], $expandParameters)
        );

        $this->client->log("Result=\n" . $result);

        return $this->client->map($result, $request);
    }

    /**
     * @return Request[]
     *
     * @throws JsonMapper_Exception
     * @throws JiraException
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/request-getMyCustomerRequests
     */
    public function getRequestsByCustomer(Customer $customer, array $searchParameters): array
    {
        $defaultSearchParameters = [
            'serviceDeskId' => $this->serviceDeskId,
            'requestOwnership' => 'OWNED_REQUESTS',
            'start' => 0,
            'limit' => 50,
            'searchTerm' => $customer->name,
        ];

        $searchParameters = array_merge($defaultSearchParameters, $searchParameters);

        $result = $this->client->exec(
            $this->client->createUrl('%s?%s', [$this->uri,], $searchParameters)
        );

        $requestData = json_decode($result, false);
        $requests = [];

        foreach ($requestData->values as $request) {
            $requests[] = $this->client->mapWithoutDecode(
                $request,
                new Request()
            );
        }

        return $requests;
    }

    /**
     * @throws JiraException
     * @throws JsonMapper_Exception
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/request-createCustomerRequest
     */
    public function create(Request $request): Request
    {
        $request->serviceDeskId = $this->serviceDeskId;

        $data = json_encode($request);

        $this->client->log("Create ServiceDeskRequest=\n" . $data);

        $result = $this->client->exec($this->uri, $data, 'POST');

        return $this->client->map(
            $result,
            new Request()
        );
    }

    /**
     * Add one or more file to an request.
     *
     * @param Attachment[] $attachments
     *
     * @return Attachment[]
     * @throws JsonMapper_Exception
     * @throws JiraException
     */
    public function addAttachments(int $requestId, array $attachments): array
    {
        $temporaryFileNames = $this->attachmentService->createTemporaryFiles($attachments);

        $attachments = $this->attachmentService->addAttachmentToRequest($requestId, $temporaryFileNames);

        $this->client->log('addAttachments result=' . var_export($attachments, true));

        return $attachments;
    }

    /**
     * @throws JiraException
     * @throws JsonMapper_Exception
     */
    public function addComment(int $issueId, Comment $comment): Comment
    {
        return $this->commentService->addComment($issueId, $comment);
    }

    /**
     * @throws JsonMapper_Exception
     * @throws JiraException
     */
    public function getComment(int $issueId, int $commentId): Comment
    {
        return $this->commentService->getComment($issueId, $commentId);
    }

    /**
     * @return Comment[]
     *
     * @throws JiraException
     * @throws JsonMapper_Exception
     * @throws InvalidArgumentException
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/request/{issueIdOrKey}/comment-getRequestComments
     */
    public function getCommentsForRequest(
        int $issueId,
        bool $showPublicComments = true,
        bool $showInternalComments = true,
        int $startIndex = 0,
        int $amountOfItems = 50
    ): array
    {
        return $this->commentService->getCommentsForRequest($issueId, $showPublicComments, $showInternalComments, $startIndex, $amountOfItems);
    }

    /**
     * Change a issue assignee.
     *
     * @param string|int $issueIdOrKey
     * @param string|null $assigneeName Assigns an issue to a user.
     *                                  If the assigneeName is "-1" automatic assignee is used.
     *                                  A null name will remove the assignee.
     *
     * @return string|bool
     * @throws JiraException
     *
     */
    public function changeAssignee($issueIdOrKey, string $assigneeName)
    {
        $this->client->log("changeAssignee=\n");

        $ar = ['name' => $assigneeName];

        $data = json_encode($ar);

        $ret = $this->exec($this->uri . "/$issueIdOrKey/assignee", $data, 'PUT');

        $this->log->info(
            'change assignee of ' . $issueIdOrKey . ' to ' . $assigneeName . ' result=' . var_export($ret, true)
        );

        return $ret;
    }

    /**
     * Change a issue assignee for REST API V3.
     *
     * @param string|int $issueIdOrKey
     *
     * @throws JiraException
     */
    public function changeAssigneeByAccountId($issueIdOrKey, ?string $accountId): string
    {
        $this->log->info("changeAssigneeByAccountId=\n");

        $ar = ['accountId' => $accountId];

        $data = json_encode($ar);

        $ret = $this->exec($this->uri . "/$issueIdOrKey/assignee", $data, 'PUT');

        $this->log->info(
            'change assignee of ' . $issueIdOrKey . ' to ' . $accountId . ' result=' . var_export($ret, true)
        );

        return $ret;
    }

    /**
     * Delete a issue.
     *
     * @param string|int $issueIdOrKey Issue id or key
     *
     * @return string|bool
     * @throws JiraException
     */
    public function deleteRequest($issueIdOrKey, array $paramArray = [])
    {
        $this->log->info("deleteIssue=\n");

        $queryParam = '?' . http_build_query($paramArray);

        $ret = $this->exec($this->uri . "/$issueIdOrKey" . $queryParam, '', 'DELETE');

        $this->log->info('delete issue ' . $issueIdOrKey . ' result=' . var_export($ret, true));

        return $ret;
    }

    /**
     * Get a list of the transitions possible for this issue by the current user, along with fields that are required and their types.
     *
     * @param string|int $issueIdOrKey Issue id or key
     *
     * @return Transition[] array of Transition class
     * @throws JiraException
     * @throws JsonMapper_Exception
     */
    public function getTransition($issueIdOrKey, array $paramArray = []): array
    {
        $queryParam = '?' . http_build_query($paramArray);

        $ret = $this->exec($this->uri . "/$issueIdOrKey/transitions" . $queryParam);

        $this->log->debug('getTransitions result=' . var_export($ret, true));

        $data = json_encode(json_decode($ret)->transitions);

        return $this->json_mapper->mapArray(
            json_decode($data),
            new ArrayObject(),
            Transition::class
        );
    }

    /**
     * find transition id by transition's to field name(aka 'Resolved').
     *
     * @param string|int $issueIdOrKey
     *
     * @throws JiraException
     * @throws JsonMapper_Exception
     */
    public function findTransitonId($issueIdOrKey, string $transitionToName): string
    {
        $this->log->debug('findTransitonId=');

        $ret = $this->getTransition($issueIdOrKey);

        foreach ($ret as $trans) {
            $toName = $trans->to->name;

            $this->log->debug('getTransitions result=' . var_export($ret, true));

            if (strcasecmp($toName, $transitionToName) === 0) {
                return $trans->id;
            }
        }

        throw new JiraException(
            sprintf("Transition name '%s' not found on JIRA Server.", $transitionToName)
        );
    }

    /**
     * Perform a transition on an issue.
     *
     * @param string|int $issueIdOrKey Issue id or key
     *
     * @return string|null nothing - if transition was successful return http 204(no contents)
     *
     * @throws JiraException
     * @throws JsonMapper_Exception
     */
    public function transition($issueIdOrKey, Transition $transition): ?string
    {
        $this->log->debug('transition=' . var_export($transition, true));

        if (!isset($transition->transition['id'])) {
            if (isset($transition->transition['untranslatedName'])) {
                $transition->transition['id'] = $this->findTransitonIdByUntranslatedName(
                    $issueIdOrKey,
                    $transition->transition['untranslatedName']
                );
            } elseif (isset($transition->transition['name'])) {
                $transition->transition['id'] = $this->findTransitonId($issueIdOrKey, $transition->transition['name']);
            } else {
                throw new JiraException('you must set either name or untranslatedName for performing transition.');
            }
        }

        $data = json_encode($transition);

        $this->log->debug("transition req=$data\n");

        $ret = $this->exec($this->uri . "/$issueIdOrKey/transitions", $data, 'POST');

        $this->log->debug('getTransitions result=' . var_export($ret, true));

        return $ret;
    }

    /**
     * get TimeTracking info.
     *
     * @param string|int $issueIdOrKey
     *
     * @throws JsonMapper_Exception
     * @throws JiraException
     */
    public function getTimeTracking($issueIdOrKey): TimeTracking
    {
        $ret = $this->exec($this->uri . "/$issueIdOrKey", null);
        $this->log->debug("getTimeTracking res=$ret\n");

        $issue = $this->json_mapper->map(
            json_decode($ret),
            new Issue()
        );

        return $issue->fields->timeTracking;
    }

    /**
     * TimeTracking issues.
     *
     * @param string|int $issueIdOrKey Issue id or key
     *
     * @throws JiraException
     */
    public function timeTracking($issueIdOrKey, TimeTracking $timeTracking): string
    {
        $array = [
            'update' => [
                'timetracking' => [
                    ['edit' => $timeTracking],
                ],
            ],
        ];

        $data = json_encode($array);

        $this->log->debug("TimeTracking req=$data\n");

        // if success, just return HTTP 201.
        return $this->exec($this->uri . "/$issueIdOrKey", $data, 'PUT');
    }

    /**
     * get getWorklog.
     *
     * @param string|int $issueIdOrKey
     * @param array $paramArray Possible keys for $paramArray: 'startAt', 'maxResults', 'startedAfter', 'expand'
     *
     * @throws JsonMapper_Exception
     * @throws JiraException
     */
    public function getWorklog($issueIdOrKey, array $paramArray = []): PaginatedWorklog
    {
        $ret = $this->exec($this->uri . "/$issueIdOrKey/worklog" . $this->toHttpQueryParameter($paramArray));
        $this->log->debug("getWorklog res=$ret\n");

        return $this->json_mapper->map(
            json_decode($ret),
            new PaginatedWorklog()
        );
    }

    /**
     * get getWorklog by Id.
     *
     * @param string|int $issueIdOrKey
     *
     * @throws JsonMapper_Exception
     * @throws JiraException
     */
    public function getWorklogById($issueIdOrKey, int $workLogId): Worklog
    {
        $ret = $this->exec($this->uri . "/$issueIdOrKey/worklog/$workLogId");
        $this->log->debug("getWorklogById res=$ret\n");

        return $this->json_mapper->map(
            json_decode($ret),
            new Worklog()
        );
    }

    /**
     * add work log to issue.
     *
     * @param string|int $issueIdOrKey
     *
     * @throws JsonMapper_Exception
     * @throws JiraException
     */
    public function addWorklog($issueIdOrKey, Worklog $worklog): Worklog
    {
        $this->log->info("addWorklog=\n");

        $data = json_encode($worklog);
        $url = $this->uri . "/$issueIdOrKey/worklog";
        $type = 'POST';

        $ret = $this->exec($url, $data, $type);

        return $this->json_mapper->map(
            json_decode($ret),
            new Worklog()
        );
    }

    /**
     * edit the worklog.
     *
     * @param string|int $issueIdOrKey
     *
     * @throws JsonMapper_Exception
     * @throws JiraException
     */
    public function editWorklog($issueIdOrKey, Worklog $worklog, int $worklogId): Worklog
    {
        $this->log->info("editWorklog=\n");

        $data = json_encode($worklog);
        $url = $this->uri . "/$issueIdOrKey/worklog/$worklogId";
        $type = 'PUT';

        $ret = $this->exec($url, $data, $type);

        return $this->json_mapper->map(
            json_decode($ret),
            new Worklog()
        );
    }

    /**
     * delete worklog.
     *
     * @param string|int $issueIdOrKey
     *
     * @throws JiraException
     */
    public function deleteWorklog($issueIdOrKey, int $worklogId): bool
    {
        $this->log->info("deleteWorklog=\n");

        $url = $this->uri . "/$issueIdOrKey/worklog/$worklogId";
        $type = 'DELETE';

        $ret = $this->exec($url, null, $type);

        return (bool)$ret;
    }

    /**
     * Get all priorities.
     *
     * @return Priority[] array of priority class
     *
     * @throws JiraException
     * @throws JsonMapper_Exception
     */
    public function getAllPriorities(): array
    {
        $ret = $this->exec('priority', null);

        return $this->json_mapper->mapArray(
            json_decode($ret, false),
            new ArrayObject(),
            Priority::class
        );
    }

    /**
     * Get priority by id.
     * throws  HTTPException if the priority is not found, or the calling user does not have permission or view it.
     *
     * @throws JsonMapper_Exception
     * @throws JiraException
     */
    public function getPriority(int $priorityId): Priority
    {
        $ret = $this->exec("priority/$priorityId", null);

        $this->log->info('Result=' . $ret);

        return $this->json_mapper->map(
            json_decode($ret),
            new Priority()
        );
    }

    /**
     * Get priority by id.
     * throws HTTPException if the priority is not found, or the calling user does not have permission or view it.
     *
     * @throws JsonMapper_Exception
     * @throws JiraException
     */
    public function getCustomFields(int $priorityId): Priority
    {
        $ret = $this->exec("priority/$priorityId", null);

        $this->log->info('Result=' . $ret);

        return $this->json_mapper->map(
            json_decode($ret),
            new Priority()
        );
    }

    /**
     * get watchers.
     *
     * @param string $issueIdOrKey
     *
     * @return Reporter[]
     *
     * @throws JsonMapper_Exception
     * @throws JiraException
     */
    public function getWatchers(string $issueIdOrKey): array
    {
        $this->log->info("getWatchers=\n");

        $url = $this->uri . "/$issueIdOrKey/watchers";

        $ret = $this->exec($url, null);

        return $this->json_mapper->mapArray(
            json_decode($ret, false)->watchers,
            new ArrayObject(),
            '\JiraRestApi\Issue\Reporter'
        );
    }

    /**
     * add watcher to issue.
     *
     * @param string|int $issueIdOrKey
     *
     * @throws JiraException
     */
    public function addWatcher($issueIdOrKey, string $watcher): bool
    {
        $this->log->info("addWatcher=\n");

        $data = json_encode($watcher);
        $url = $this->uri . "/$issueIdOrKey/watchers";
        $type = 'POST';

        $this->exec($url, $data, $type);

        return $this->http_response == 204;
    }

    /**
     * remove watcher from issue.
     *
     * @param string|int $issueIdOrKey
     *
     * @throws JiraException
     */
    public function removeWatcher($issueIdOrKey, string $watcher): bool
    {
        $this->log->debug("removeWatcher=\n");

        $ret = $this->exec($this->uri . "/$issueIdOrKey/watchers/?username=$watcher", '', 'DELETE');

        $this->log->debug('remove watcher ' . $issueIdOrKey . ' result=' . var_export($ret, true));

        return $this->http_response == 204;
    }

    /**
     * remove watcher from issue by watcher account id.
     *
     * @param string|int $issueIdOrKey
     *
     * @throws JiraException
     */
    public function removeWatcherByAccountId($issueIdOrKey, string $accountId): bool
    {
        $this->log->debug("removeWatcher=\n");

        $ret = $this->exec($this->uri . "/$issueIdOrKey/watchers/?accountId=$accountId", '', 'DELETE');

        $this->log->debug('remove watcher ' . $issueIdOrKey . ' result=' . var_export($ret, true));

        return $this->http_response == 204;
    }

    /**
     * Get the meta data for creating issues.
     *
     * @param array $paramArray Possible keys for $paramArray: 'projectIds', 'projectKeys', 'issuetypeIds', 'issuetypeNames'.
     *
     * @return object array of meta data for creating issues.
     *
     * @throws JiraException
     *
     */
    public function getCreateMeta(array $paramArray = [], bool $retrieveAllFieldsAndValues = true)
    {
        $paramArray['expand'] = ($retrieveAllFieldsAndValues) ? 'projects.issuetypes.fields' : null;
        $paramArray = array_filter($paramArray);

        $queryParam = '?' . http_build_query($paramArray);

        $ret = $this->exec($this->uri . '/createmeta' . $queryParam, null);

        return json_decode($ret);
    }

    /**
     * returns the metadata(include custom field) for an issue.
     *
     * @return array of custom fields
     *
     * @throws JiraException
     *
     * @see https://confluence.atlassian.com/jirakb/how-to-retrieve-available-options-for-a-multi-select-customfield-via-jira-rest-api-815566715.html How to retrieve available options for a multi-select customfield via JIRA REST API
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/#api-api-2-issue-issueIdOrKey-editmeta-get
     */
    public function getEditMeta(
        string $idOrKey,
        bool $overrideEditableFlag = false,
        bool $overrideScreenSecurity = false
    ): array {
        $queryParam = '?' . http_build_query(
                [
                    'overrideEditableFlag' => $overrideEditableFlag,
                    'overrideScreenSecurity' => $overrideScreenSecurity,
                ]
            );

        $uri = sprintf('%s/%s/editmeta', $this->uri, $idOrKey) . $queryParam;

        $ret = $this->exec($uri, null);

        $metas = json_decode($ret, true);

        return array_filter(
            $metas['fields'],
            static function ($key) {
                $pos = strpos($key, 'customfield');

                return $pos !== false;
            },
            ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Sends a notification (email) to the list or recipients defined in the request.
     *
     * @param string|int $issueIdOrKey Issue id Or Key
     *
     * @throws JiraException
     *
     * @see https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-notify
     */
    public function notify($issueIdOrKey, Notify $notify): void
    {
        $full_uri = $this->uri . "/$issueIdOrKey/notify";

        $notify->to['groups'] = $this->notifySetSelf($notify->to['groups']);
        $notify->restrict['groups'] = $this->notifySetSelf($notify->restrict['groups']);

        $data = json_encode($notify, JSON_UNESCAPED_SLASHES);

        $this->log->debug("notify=$data\n");

        $ret = $this->exec($full_uri, $data, 'POST');

        if ($ret !== true) {
            throw new JiraException('notify failed: response code=' . $ret);
        }
    }

    /**
     * Get a remote issue links on the issue.
     *
     * @param string|int $issueIdOrKey Issue id Or Key
     *
     * @return RemoteIssueLink[]
     *
     * @throws JiraException
     * @throws JsonMapper_Exception
     *
     * @see https://developer.atlassian.com/server/jira/platform/jira-rest-api-for-remote-issue-links/
     * @see https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-getRemoteIssueLinks
     */
    public function getRemoteIssueLink($issueIdOrKey): array
    {
        $full_uri = $this->uri . "/$issueIdOrKey/remotelink";

        $ret = $this->exec($full_uri, null);

        return $this->json_mapper->mapArray(
            json_decode($ret, false),
            new ArrayObject(),
            RemoteIssueLink::class
        );
    }

    /**
     * @param string|int $issueIdOrKey
     *
     * @throws JiraException
     * @throws JsonMapper_Exception
     */
    public function createOrUpdateRemoteIssueLink($issueIdOrKey, RemoteIssueLink $remoteIssueLink): RemoteIssueLink
    {
        $full_uri = $this->uri . "/$issueIdOrKey/remotelink";

        $data = json_encode($remoteIssueLink, JSON_UNESCAPED_SLASHES);

        $this->log->debug("create remoteIssueLink=$data\n");

        $ret = $this->exec($full_uri, $data, 'POST');

        return $this->json_mapper->map(
            json_decode($ret),
            new RemoteIssueLink()
        );
    }

    /**
     * @param string|int $issueIdOrKey
     * @param string|int $globalId
     *
     * @return string|bool
     *
     * @throws JiraException
     */
    public function removeRemoteIssueLink($issueIdOrKey, $globalId)
    {
        $query = http_build_query(['globalId' => $globalId]);

        $full_uri = sprintf('%s/%s/remotelink?%s', $this->uri, $issueIdOrKey, $query);

        $ret = $this->exec($full_uri, '', 'DELETE');

        $this->log->info(
            sprintf(
                'delete remote issue link for issue "%s" with globalId "%s". Result=%s',
                $issueIdOrKey,
                $globalId,
                var_export($ret, true)
            )
        );

        return $ret;
    }

    /**
     * get all issue security schemes.
     *
     * @return SecurityScheme[] array of SecurityScheme class
     *
     * @throws JsonMapper_Exception
     * @throws JiraException
     */
    public function getAllIssueSecuritySchemes(): array
    {
        $url = '/issuesecurityschemes';

        $ret = $this->exec($url);

        $data = json_decode($ret, true);

        $schemes = json_decode(json_encode($data['issueSecuritySchemes']), false);

        return $this->json_mapper->mapArray(
            $schemes,
            new ArrayObject(),
            '\JiraRestApi\Issue\SecurityScheme'
        );
    }

    /**
     *  get issue security scheme.
     *
     * @throws JsonMapper_Exception
     * @throws JiraException
     */
    public function getIssueSecuritySchemes(int $securityId): SecurityScheme
    {
        $url = '/issuesecurityschemes/' . $securityId;

        $ret = $this->exec($url);

        return $this->json_mapper->map(
            json_decode($ret),
            new SecurityScheme()
        );
    }

    /**
     * convenient wrapper function for add or remove labels.
     *
     * @param string|int $issueIdOrKey
     *
     * @throws JiraException
     */
    public function updateLabels(
        $issueIdOrKey,
        array $addLabelsParameters,
        array $removeLabelsParameters,
        bool $notifyUsers = true
    ): bool
    {
        $labels = [];
        foreach ($addLabelsParameters as $a) {
            array_push($labels, ['add' => $a]);
        }

        foreach ($removeLabelsParameters as $r) {
            array_push($labels, ['remove' => $r]);
        }

        $postData = json_encode(
            [
                'update' => [
                    'labels' => $labels,
                ],
            ],
            JSON_UNESCAPED_UNICODE
        );

        $this->log->info("Update labels=\n" . $postData);

        $queryParam = '?' . http_build_query(['notifyUsers' => $notifyUsers]);

        return $this->exec($this->uri . "/$issueIdOrKey" . $queryParam, $postData, 'PUT');
    }

    /**
     * find transition id by transition's untranslatedName.
     *
     * @param string|int $issueIdOrKey
     *
     * @throws JiraException
     */
    public function findTransitonIdByUntranslatedName($issueIdOrKey, string $untranslatedName): string
    {
        $this->log->debug('findTransitonIdByUntranslatedName=');

        $project = new ProjectService($this->getConfiguration());
        $projectKey = explode('-', $issueIdOrKey);
        $transitions = $project->getProjectTransitionsToArray($projectKey[0]);

        $this->log->debug('getTransitions result=' . var_export($transitions, true));

        foreach ($transitions as $trans) {
            if (
                strcasecmp($trans['name'], $untranslatedName) === 0 ||
                strcasecmp($trans['untranslatedName'] ?? '', $untranslatedName) === 0
            ) {
                return $trans['id'];
            }
        }

        throw new JiraException(sprintf("Transition name '%s' not found on JIRA Server.", $untranslatedName));
    }

    private function notifySetSelf(array $groups): array
    {
        foreach ($groups as $key => $group) {
            $groups[$key]['self'] = sprintf(
                '%s/rest/api/2/group?groupname=%s',
                $this->getConfiguration()->getJiraHost(),
                $group['name']
            );
        }

        return $groups;
    }
}