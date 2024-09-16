<?php

declare(strict_types=1);

namespace JiraRestApi\ServiceDesk\Request;

use InvalidArgumentException;
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
use JiraRestApi\Project\ProjectService;
use JiraRestApi\ServiceDesk\Attachment\AttachmentService;
use JiraRestApi\ServiceDesk\Comment\Comment;
use JiraRestApi\ServiceDesk\Comment\CommentService;
use JiraRestApi\ServiceDesk\Customer\Customer;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JsonException;
use JsonMapper;
use JsonMapper_Exception;
use Psr\Log\LoggerInterface;

class RequestService
{
    private ServiceDeskClient $client;
    private CommentService $commentService;
    private AttachmentService $attachmentService;
    private LoggerInterface $logger;
    private JsonMapper $jsonMapper;
    private string $uri = '/request';

    public function __construct(
        ServiceDeskClient $client,
        CommentService $commentService,
        AttachmentService $attachmentService
    ) {
        $this->client = $client;
        $this->commentService = $commentService;
        $this->attachmentService = $attachmentService;
        $this->logger = $client->getLogger();
        $this->jsonMapper = $client->getMapper();
    }

    /**
     * @throws JsonMapper_Exception
     */
    public function getRequestFromJSON(object $jsonData): Request
    {
        return $this->jsonMapper->map(
            $jsonData,
            new Request()
        );
    }

    /**
     * @throws JiraException|JsonMapper_Exception|JsonException
     *
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/request-getCustomerRequestByIdOrKey
     */
    public function get(string $issueId, array $expandParameters = [], Request $request = null): Request
    {
        $request = ($request) ?: new Request();

        $result = $this->client->exec(
            $this->client->createUrl('%s/%s?%s', [$this->uri, $issueId], $expandParameters)
        );

        $this->logger->info("Result=\n".$result);

        return $this->jsonMapper->map(
            json_decode($result, false, 512, JSON_THROW_ON_ERROR),
            $request
        );
    }

    /**
     * @throws JsonMapper_Exception|JiraException|JsonException
     *
     * @return Request[]
     *
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/request-getMyCustomerRequests
     */
    public function getRequestsByCustomer(Customer $customer, array $searchParameters, int $serviceDeskId = null): array
    {
        $defaultSearchParameters = [
            'requestOwnership' => 'OWNED_REQUESTS',
            'start'            => 0,
            'limit'            => 50,
            'searchTerm'       => $customer->name,
        ];

        if ($serviceDeskId !== null) {
            $defaultSearchParameters['serviceDeskId'] = $serviceDeskId;
        }

        $searchParameters = array_merge($defaultSearchParameters, $searchParameters);

        $result = $this->client->exec(
            $this->client->createUrl('%s?%s', [$this->uri], $searchParameters)
        );

        $requestData = json_decode($result, false, 512, JSON_THROW_ON_ERROR);
        $requests = [];

        foreach ($requestData->values as $request) {
            $requests[] = $this->jsonMapper->map(
                $request,
                new Request()
            );
        }

        return $requests;
    }

    /**
     * @throws JiraException|JsonMapper_Exception|JsonException|InvalidArgumentException
     *
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/request-createCustomerRequest
     */
    public function create(Request $request): Request
    {
        if (empty($request->serviceDeskId)) {
            throw new InvalidArgumentException('Service desk ID is not set.');
        }

        $data = json_encode($request, JSON_THROW_ON_ERROR);

        $this->logger->info("Create ServiceDeskRequest=\n".$data);

        $result = $this->client->exec($this->uri, $data, 'POST');

        return $this->jsonMapper->map(
            json_decode($result, false, 512, JSON_THROW_ON_ERROR),
            new Request()
        );
    }

    /**
     * Add one or more file to a request.
     *
     * @param Attachment[] $attachments
     *
     * @throws JsonMapper_Exception|JiraException|JsonException
     *
     * @return Attachment[]
     */
    public function addAttachments(string $serviceDeskId, int $requestId, array $attachments): array
    {
        $temporaryFileNames = $this->attachmentService->createTemporaryFiles($attachments, $serviceDeskId);

        $attachments = $this->attachmentService->addAttachmentToRequest($requestId, $temporaryFileNames);

        $this->logger->info('addAttachments result='.var_export($attachments, true));

        return $attachments;
    }

    /**
     * @throws JiraException|JsonMapper_Exception|JsonException
     */
    public function addComment(string $issueId, Comment $comment): Comment
    {
        return $this->commentService->addComment($issueId, $comment);
    }

    /**
     * @throws JsonMapper_Exception|JiraException|JsonException
     */
    public function getComment(string $issueId, int $commentId): Comment
    {
        return $this->commentService->getComment($issueId, $commentId);
    }

    /**
     * @throws JiraException|JsonMapper_Exception|JsonException|InvalidArgumentException
     *
     * @return Comment[]
     *
     * @see https://docs.atlassian.com/jira-servicedesk/REST/3.6.2/#servicedeskapi/request/{issueIdOrKey}/comment-getRequestComments
     */
    public function getCommentsForRequest(string $issueId, bool $showPublicComments = true, bool $showInternalComments = true, int $startIndex = 0, int $amountOfItems = 50): array
    {
        return $this->commentService->getCommentsForRequest(
            $issueId,
            $showPublicComments,
            $showInternalComments,
            $startIndex,
            $amountOfItems
        );
    }

    /**
     * Change an issue assignee.
     *
     * @param string|null $assigneeName Assigns an issue to a user.
     *                                  If the assigneeName is "-1" automatic assignee is used.
     *                                  A null name will remove the assignee.
     *
     * @throws JiraException|JsonException
     */
    public function changeAssignee(string $issueIdOrKey, ?string $assigneeName): string|bool
    {
        $this->logger->info("changeAssignee=\n");

        $ar = ['name' => $assigneeName];

        $data = json_encode($ar, JSON_THROW_ON_ERROR);

        $ret = $this->client->exec($this->uri."/$issueIdOrKey/assignee", $data, 'PUT');

        $this->logger->info(
            'change assignee of '.$issueIdOrKey.' to '.$assigneeName.' result='.var_export($ret, true)
        );

        return $ret;
    }

    /**
     * Change an issue assignee for REST API V3.
     *
     * @throws JiraException|JsonException
     */
    public function changeAssigneeByAccountId(string $issueIdOrKey, ?string $accountId): string
    {
        $this->logger->info("changeAssigneeByAccountId=\n");

        $ar = ['accountId' => $accountId];

        $data = json_encode($ar, JSON_THROW_ON_ERROR);

        $ret = $this->client->exec($this->uri."/$issueIdOrKey/assignee", $data, 'PUT');

        $this->logger->info(
            'change assignee of '.$issueIdOrKey.' to '.$accountId.' result='.var_export($ret, true)
        );

        return $ret;
    }

    /**
     * Delete an issue.
     *
     * @throws JiraException
     */
    public function deleteRequest(string|int $issueIdOrKey, array $paramArray = []): string|bool
    {
        $this->logger->info("deleteIssue=\n");

        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->client->exec($this->uri."/$issueIdOrKey".$queryParam, '', 'DELETE');

        $this->logger->info('delete issue '.$issueIdOrKey.' result='.var_export($ret, true));

        return $ret;
    }

    /**
     * Get a list of the transitions possible for this issue by the current user, along with fields that are required and their types.
     *
     * @throws JiraException|JsonMapper_Exception|JsonException
     *
     * @return Transition[] array of Transition class
     */
    public function getTransition(string $issueIdOrKey, array $paramArray = []): array
    {
        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->client->exec($this->uri."/$issueIdOrKey/transitions".$queryParam);

        $this->logger->info('getTransitions result='.var_export($ret, true));

        $data = json_encode(
            json_decode($ret, false, 512, JSON_THROW_ON_ERROR)->transitions,
            JSON_THROW_ON_ERROR
        );

        return $this->jsonMapper->mapArray(
            json_decode($data, false, 512, JSON_THROW_ON_ERROR),
            [],
            Transition::class
        );
    }

    /**
     * find transition id by transition's to field name(aka 'Resolved').
     *
     * @throws JiraException|JsonMapper_Exception|JsonException
     */
    public function findTransitionId(string|int $issueIdOrKey, string $transitionToName): string
    {
        $this->logger->info('findTransitonId=');

        $ret = $this->getTransition($issueIdOrKey);
        $this->logger->info('getTransitions result='.var_export($ret, true));

        foreach ($ret as $trans) {
            $toName = $trans->to->name;

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
     * @throws JiraException|JsonMapper_Exception|JsonException
     *
     * @return string|null nothing - if transition was successful return http 204(no contents)
     */
    public function transition(string $issueIdOrKey, Transition $transition): ?string
    {
        $this->logger->info('transition='.var_export($transition, true));

        if (!isset($transition->transition['id'])) {
            if (isset($transition->transition['untranslatedName'])) {
                $transition->transition['id'] = $this->findTransitionIdByUntranslatedName(
                    $issueIdOrKey,
                    $transition->transition['untranslatedName']
                );
            } elseif (isset($transition->transition['name'])) {
                $transition->transition['id'] = $this->findTransitionId($issueIdOrKey, $transition->transition['name']);
            } else {
                throw new JiraException('you must set either name or untranslatedName for performing transition.');
            }
        }

        $data = json_encode($transition, JSON_THROW_ON_ERROR);

        $this->logger->info("transition req=$data\n");

        $ret = $this->client->exec($this->uri."/$issueIdOrKey/transitions", $data, 'POST');

        $this->logger->info('getTransitions result='.var_export($ret, true));

        return $ret;
    }

    /**
     * get TimeTracking info.
     *
     * @throws JsonMapper_Exception|JiraException|JsonException
     */
    public function getTimeTracking(string $issueIdOrKey): TimeTracking
    {
        $ret = $this->client->exec($this->uri."/$issueIdOrKey");
        $this->logger->info("getTimeTracking res=$ret\n");

        $issue = $this->jsonMapper->map(
            json_decode($ret, false, 512, JSON_THROW_ON_ERROR),
            new Issue()
        );

        return $issue->fields->timeTracking;
    }

    /**
     * TimeTracking issues.
     *
     * @throws JiraException|JsonException
     */
    public function timeTracking(string $issueIdOrKey, TimeTracking $timeTracking): string
    {
        $array = [
            'update' => [
                'timetracking' => [
                    ['edit' => $timeTracking],
                ],
            ],
        ];

        $data = json_encode($array, JSON_THROW_ON_ERROR);

        $this->logger->info("TimeTracking req=$data\n");

        // if success, just return HTTP 201.
        return $this->client->exec($this->uri."/$issueIdOrKey", $data, 'PUT');
    }

    /**
     * get worklog.
     *
     * @param array $paramArray Possible keys for $paramArray: 'startAt', 'maxResults', 'startedAfter', 'expand'
     *
     * @throws JsonMapper_Exception|JiraException|JsonException
     */
    public function getWorklog(string $issueIdOrKey, array $paramArray = []): PaginatedWorklog
    {
        $ret = $this->client->exec(
            $this->uri."/$issueIdOrKey/worklog".$this->client->toHttpQueryParameter($paramArray)
        );
        $this->logger->debug("getWorklog res=$ret\n");

        return $this->jsonMapper->map(
            json_decode($ret, false, 512, JSON_THROW_ON_ERROR),
            new PaginatedWorklog()
        );
    }

    /**
     * get worklog by Id.
     *
     * @throws JsonMapper_Exception|JiraException|JsonException
     */
    public function getWorklogById(string $issueIdOrKey, int $workLogId): Worklog
    {
        $ret = $this->client->exec($this->uri."/$issueIdOrKey/worklog/$workLogId");
        $this->logger->debug("getWorklogById res=$ret\n");

        return $this->jsonMapper->map(
            json_decode($ret, false, 512, JSON_THROW_ON_ERROR),
            new Worklog()
        );
    }

    /**
     * @param array<int> $ids
     *
     * @return array<Worklog>
     */
    public function getWorklogsByIds(array $ids): array
    {
        $ret = $this->client->exec('/worklog/list', json_encode(['ids' => $ids]), 'POST');

        $this->logger->debug("getWorklogsByIds res=$ret\n");

        $worklogsResponse = json_decode($ret, false, 512, JSON_THROW_ON_ERROR);

        $worklogs = array_map(function ($worklog) {
            return $this->jsonMapper->map($worklog, new Worklog());
        }, $worklogsResponse);

        return $worklogs;
    }

    /**
     * add work log to issue.
     *
     * @throws JsonMapper_Exception|JiraException|JsonException
     */
    public function addWorklog(string $issueIdOrKey, Worklog $worklog): Worklog
    {
        $this->logger->info("addWorklog=\n");

        $data = json_encode($worklog, JSON_THROW_ON_ERROR);
        $url = $this->uri."/$issueIdOrKey/worklog";
        $type = 'POST';

        $ret = $this->client->exec($url, $data, $type);

        return $this->jsonMapper->map(
            json_decode($ret, false, 512, JSON_THROW_ON_ERROR),
            new Worklog()
        );
    }

    /**
     * edit the worklog.
     *
     * @throws JsonMapper_Exception|JiraException|JsonException
     */
    public function editWorklog(string $issueIdOrKey, Worklog $worklog, int $worklogId): Worklog
    {
        $this->logger->info("editWorklog=\n");

        $data = json_encode($worklog, JSON_THROW_ON_ERROR);
        $url = $this->uri."/$issueIdOrKey/worklog/$worklogId";
        $type = 'PUT';

        $ret = $this->client->exec($url, $data, $type);

        return $this->jsonMapper->map(
            json_decode($ret, false, 512, JSON_THROW_ON_ERROR),
            new Worklog()
        );
    }

    /**
     * delete worklog.
     *
     * @throws JiraException
     */
    public function deleteWorklog(string|int $issueIdOrKey, int $worklogId): bool
    {
        $this->logger->info("deleteWorklog=\n");

        $url = $this->uri."/$issueIdOrKey/worklog/$worklogId";
        $type = 'DELETE';

        $ret = $this->client->exec($url, null, $type);

        return (bool) $ret;
    }

    /**
     * Get all priorities.
     *
     * @throws JiraException|JsonMapper_Exception|JsonException
     *
     * @return Priority[] array of priority class
     */
    public function getAllPriorities(): array
    {
        $ret = $this->client->exec('priority');

        return $this->jsonMapper->mapArray(
            json_decode($ret, false, 512, JSON_THROW_ON_ERROR),
            [],
            Priority::class
        );
    }

    /**
     * Get priority by id.
     * throws  HTTPException if the priority is not found, or the calling user does not have permission or view it.
     *
     * @throws JsonMapper_Exception|JiraException|JsonException
     */
    public function getPriority(int $priorityId): Priority
    {
        $ret = $this->client->exec("priority/$priorityId");

        $this->logger->info('Result='.$ret);

        return $this->jsonMapper->map(
            json_decode($ret, false, 512, JSON_THROW_ON_ERROR),
            new Priority()
        );
    }

    /**
     * get watchers.
     *
     * @throws JsonMapper_Exception|JiraException|JsonException
     *
     * @return Reporter[]
     */
    public function getWatchers(string $issueIdOrKey): array
    {
        $this->logger->info("getWatchers=\n");

        $url = $this->uri."/$issueIdOrKey/watchers";

        $ret = $this->client->exec($url);

        return $this->jsonMapper->mapArray(
            json_decode($ret, false, 512, JSON_THROW_ON_ERROR)->watchers,
            [],
            Reporter::class
        );
    }

    /**
     * add watcher to issue.
     *
     * @throws JiraException|JsonException
     */
    public function addWatcher(string $issueIdOrKey, string $watcher): bool
    {
        $this->logger->info("addWatcher=\n");

        $data = json_encode($watcher, JSON_THROW_ON_ERROR);
        $url = $this->uri."/$issueIdOrKey/watchers";
        $type = 'POST';

        $this->client->exec($url, $data, $type);

        return $this->client->getHttpResponse() == 204;
    }

    /**
     * remove watcher from issue.
     *
     * @throws JiraException
     */
    public function removeWatcher(string $issueIdOrKey, string $watcher): bool
    {
        $this->logger->debug("removeWatcher=\n");

        $ret = $this->client->exec($this->uri."/$issueIdOrKey/watchers/?username=$watcher", '', 'DELETE');

        $this->logger->debug('remove watcher '.$issueIdOrKey.' result='.var_export($ret, true));

        return $this->client->getHttpResponse() == 204;
    }

    /**
     * remove watcher from issue by watcher account id.
     *
     * @throws JiraException
     */
    public function removeWatcherByAccountId(string $issueIdOrKey, string $accountId): bool
    {
        $this->logger->debug("removeWatcher=\n");

        $ret = $this->client->exec($this->uri."/$issueIdOrKey/watchers/?accountId=$accountId", '', 'DELETE');

        $this->logger->debug('remove watcher '.$issueIdOrKey.' result='.var_export($ret, true));

        return $this->client->getHttpResponse() == 204;
    }

    /**
     * Get the metadata for creating issues.
     *
     * @param array $paramArray Possible keys for $paramArray: 'projectIds', 'projectKeys', 'issuetypeIds', 'issuetypeNames'.
     *
     * @throws JiraException|JsonException
     *
     * @return object array of metadata for creating issues.
     */
    public function getCreateMeta(array $paramArray = [], bool $retrieveAllFieldsAndValues = true): object
    {
        $paramArray['expand'] = ($retrieveAllFieldsAndValues) ? 'projects.issuetypes.fields' : null;
        $paramArray = array_filter($paramArray);

        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->client->exec($this->uri.'/createmeta'.$queryParam);

        return json_decode($ret, false, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * returns the metadata(include custom field) for an issue.
     *
     * @throws JiraException|JsonException
     *
     * @return array of custom fields
     *
     * @see https://confluence.atlassian.com/jirakb/how-to-retrieve-available-options-for-a-multi-select-customfield-via-jira-rest-api-815566715.html How to retrieve available options for a multi-select customfield via JIRA REST API
     * @see https://developer.atlassian.com/cloud/jira/platform/rest/#api-api-2-issue-issueIdOrKey-editmeta-get
     */
    public function getEditMeta(string $idOrKey, bool $overrideEditableFlag = false, bool $overrideScreenSecurity = false): array
    {
        $queryParam = '?'.http_build_query(
            [
                'overrideEditableFlag'   => $overrideEditableFlag,
                'overrideScreenSecurity' => $overrideScreenSecurity,
            ]
        );

        $uri = sprintf('%s/%s/editmeta', $this->uri, $idOrKey).$queryParam;

        $ret = $this->client->exec($uri);

        $metas = json_decode($ret, true, 512, JSON_THROW_ON_ERROR);

        return array_filter($metas['fields'], static function ($key) {
            $pos = strpos($key, 'customfield');

            return $pos !== false;
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Sends a notification (email) to the list or recipients defined in the request.
     *
     * @throws JiraException|JsonException
     *
     * @see https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-notify
     */
    public function notify(string $issueIdOrKey, Notify $notify): void
    {
        $full_uri = $this->uri."/$issueIdOrKey/notify";

        $notify->to['groups'] = $this->notifySetSelf($notify->to['groups']);
        $notify->restrict['groups'] = $this->notifySetSelf($notify->restrict['groups']);

        $data = json_encode($notify, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);

        $this->logger->debug("notify=$data\n");

        $ret = $this->client->exec($full_uri, $data, 'POST');

        if ($ret !== true) {
            throw new JiraException('notify failed: response code='.$ret);
        }
    }

    /**
     * Get a remote issue links on the issue.
     *
     * @throws JiraException|JsonMapper_Exception|JsonException
     *
     * @return RemoteIssueLink[]
     *
     * @see https://developer.atlassian.com/server/jira/platform/jira-rest-api-for-remote-issue-links/
     * @see https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/issue-getRemoteIssueLinks
     */
    public function getRemoteIssueLink(string $issueIdOrKey): array
    {
        $full_uri = $this->uri."/$issueIdOrKey/remotelink";

        $ret = $this->client->exec($full_uri, null);

        return $this->jsonMapper->mapArray(
            json_decode($ret, false, 512, JSON_THROW_ON_ERROR),
            [],
            RemoteIssueLink::class
        );
    }

    /**
     * @throws JiraException|JsonMapper_Exception|JsonException
     */
    public function createOrUpdateRemoteIssueLink(string $issueIdOrKey, RemoteIssueLink $remoteIssueLink): RemoteIssueLink
    {
        $full_uri = $this->uri."/$issueIdOrKey/remotelink";

        $data = json_encode($remoteIssueLink, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);

        $this->logger->debug("create remoteIssueLink=$data\n");

        $ret = $this->client->exec($full_uri, $data, 'POST');

        return $this->jsonMapper->map(
            json_decode($ret, false, 512, JSON_THROW_ON_ERROR),
            new RemoteIssueLink()
        );
    }

    /**
     * @throws JiraException
     */
    public function removeRemoteIssueLink(string $issueIdOrKey, string $globalId): string|bool
    {
        $query = http_build_query(['globalId' => $globalId]);

        $full_uri = sprintf('%s/%s/remotelink?%s', $this->uri, $issueIdOrKey, $query);

        $ret = $this->client->exec($full_uri, '', 'DELETE');

        $this->logger->info(
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
     * @throws JsonMapper_Exception|JiraException|JsonException
     *
     * @return SecurityScheme[] array of SecurityScheme class
     */
    public function getAllIssueSecuritySchemes(): array
    {
        $url = '/issuesecurityschemes';

        $ret = $this->client->exec($url);

        $data = json_decode($ret, true, 512, JSON_THROW_ON_ERROR);

        $schemes = json_decode(
            json_encode($data['issueSecuritySchemes'], JSON_THROW_ON_ERROR),
            false,
            512,
            JSON_THROW_ON_ERROR
        );

        return $this->jsonMapper->mapArray(
            $schemes,
            [],
            SecurityScheme::class
        );
    }

    /**
     *  get issue security scheme.
     *
     * @throws JsonMapper_Exception|JiraException|JsonException
     */
    public function getIssueSecuritySchemes(int $securityId): SecurityScheme
    {
        $url = '/issuesecurityschemes/'.$securityId;

        $ret = $this->client->exec($url);

        return $this->jsonMapper->map(
            json_decode($ret, false, 512, JSON_THROW_ON_ERROR),
            new SecurityScheme()
        );
    }

    /**
     * convenient wrapper function for add or remove labels.
     *
     * @throws JiraException|JsonException
     */
    public function updateLabels(string $issueIdOrKey, array $addLabelsParameters, array $removeLabelsParameters, bool $notifyUsers = true): bool
    {
        $labels = [];
        foreach ($addLabelsParameters as $a) {
            $labels[] = ['add' => $a];
        }

        foreach ($removeLabelsParameters as $r) {
            $labels[] = ['remove' => $r];
        }

        $postData = json_encode([
            'update' => [
                'labels' => $labels,
            ],
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        $this->logger->info("Update labels=\n".$postData);

        $queryParam = '?'.http_build_query(['notifyUsers' => $notifyUsers]);

        return $this->client->exec($this->uri."/$issueIdOrKey".$queryParam, $postData, 'PUT');
    }

    /**
     * find transition id by transition's untranslatedName.
     *
     * @throws JiraException
     */
    public function findTransitionIdByUntranslatedName(string|int $issueIdOrKey, string $untranslatedName): string
    {
        $this->logger->debug('findTransitonIdByUntranslatedName=');

        $project = new ProjectService($this->client->getConfiguration());
        $projectKey = explode('-', $issueIdOrKey);
        $transitions = $project->getProjectTransitionsToArray($projectKey[0]);

        $this->logger->debug('getTransitions result='.var_export($transitions, true));

        foreach ($transitions as $trans) {
            if (strcasecmp($trans['name'], $untranslatedName) === 0 || strcasecmp($trans['untranslatedName'] ?? '', $untranslatedName) === 0) {
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
                $this->client->getConfiguration()->getJiraHost(),
                $group['name']
            );
        }

        return $groups;
    }
}
