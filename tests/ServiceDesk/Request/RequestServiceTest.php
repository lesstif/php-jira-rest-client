<?php

declare(strict_types=1);

namespace JiraRestApi\Test\ServiceDesk\Request;

use JetBrains\PhpStorm\Pure;
use JiraRestApi\Issue\Attachment;
use JiraRestApi\Issue\Issue;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\Notify;
use JiraRestApi\Issue\RemoteIssueLink;
use JiraRestApi\Issue\TimeTracking;
use JiraRestApi\Issue\TransitionTo;
use JiraRestApi\Issue\Visibility;
use JiraRestApi\Issue\Worklog;
use JiraRestApi\ServiceDesk\Attachment\AttachmentService;
use JiraRestApi\ServiceDesk\Comment\Comment;
use JiraRestApi\ServiceDesk\Comment\CommentService;
use JiraRestApi\ServiceDesk\Customer\Customer;
use JiraRestApi\ServiceDesk\Request\Request;
use JiraRestApi\ServiceDesk\Request\RequestService;
use JiraRestApi\ServiceDesk\ServiceDeskClient;
use JsonMapper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;

class RequestServiceTest extends TestCase
{
    private ServiceDeskClient|MockObject|null $client;
    private CommentService|MockObject|null $commentService;
    private AttachmentService|MockObject|null $attachmentService;
    private ?RequestService $uut;
    private string $uri = '/request';
    private int $serviceDeskId = 10;

    public function setUp(): void
    {
        $mapper = new JsonMapper();
        $mapper->bEnforceMapType = false;

        $this->client = $this->createMock(ServiceDeskClient::class);
        $this->client->method('getMapper')->willReturn($mapper);

        $this->commentService = $this->createMock(CommentService::class);
        $this->attachmentService = $this->createMock(AttachmentService::class);

        $this->uut = new RequestService(
            $this->client,
            $this->commentService,
            $this->attachmentService
        );
    }

    public function tearDown(): void
    {
        $this->client = null;
        $this->commentService = null;
        $this->attachmentService = null;
        $this->uut = null;
    }

    public function testGet(): void
    {
        $item = [
            'issueKey' => 'rfdsf',
            'requestFieldValues' => ['key1' => 'value1', 'key2' => 'value2'],
        ];
        $url = 'https://example.com/request/get';

        $this->client->method('createUrl')
            ->with('%s/%s?%s', [$this->uri, '10'], [])
            ->willReturn($url);
        $this->client->method('exec')
            ->with($url)
            ->willReturn(json_encode($item));

        $result = $this->uut->get('10');

        self::assertSame($item['issueKey'], $result->issueKey);
        self::assertSame($item['requestFieldValues'], $result->requestFieldValues);
    }

    public function testGetRequestsByCustomer(): void
    {
        $customer = new Customer();
        $customer->name = 'Test customer';

        $items = [
            [
                'issueId' => 'dfesd',
                'issueKey' => 'dsfsdf',
            ],
            [
                'issueId' => 'hjgj',
                'issueKey' => 'gjgjg',
            ],
        ];
        $searchParameters = [
            'start' => 0,
            'limit' => 50,
            'searchTerm' => $customer->name,
            'requestOwnership' => 'OWNED_REQUESTS',
            'serviceDeskId' => $this->serviceDeskId,
        ];
        $url = 'https://example.com/request/customers';

        $this->client->method('createUrl')
            ->with('%s?%s', [$this->uri,], $searchParameters)
            ->willReturn($url);
        $this->client->method('exec')
            ->with($url)
            ->willReturn(json_encode(['values' => $items]));

        $result = $this->uut->getRequestsByCustomer($customer, [], $this->serviceDeskId);

        self::assertCount(2, $result);
    }

    public function testCreate(): void
    {
        $request = new Request();
        $request->serviceDeskId = (string) $this->serviceDeskId;
        $request->issueKey = 'asjdkhgashd';
        $request->serviceDeskId = '10';
        $request->reporter = new Customer();
        $request->reporter->emailAddress = 'Test user';

        $item = [
            'issueKey' => $request->issueKey,
            'serviceDeskId' => $request->serviceDeskId,
            'requestFieldValues' => ['test key' => 'test value'],
        ];

        $this->client->method('exec')
            ->with($this->uri, json_encode($request), 'POST')
            ->willReturn(json_encode($item));

        $result = $this->uut->create($request);

        self::assertSame($request->issueKey, $result->issueKey);
        self::assertSame($request->serviceDeskId, $result->serviceDeskId);
        self::assertSame($item['requestFieldValues'], $result->requestFieldValues);
    }

    public function testAddAttachments(): void
    {
        $attachments = [
            new Attachment(),
            new Attachment(),
        ];
        $fileNames = [
            '/uploads/file_1.png',
            '/uploads/file_2.png',
        ];

        $this->attachmentService->method('createTemporaryFiles')
            ->with($attachments)
            ->willReturn($fileNames);
        $this->attachmentService->method('addAttachmentToRequest')
            ->with(10, $fileNames)
            ->willReturn($attachments);

        $result = $this->uut->addAttachments('1', 10, $attachments);

        self::assertSame($attachments, $result);
    }

    public function testAddComment(): void
    {
        $comment = new Comment();
        $expected = new Comment();

        $this->commentService->method('addComment')
            ->with('10', $comment)
            ->willReturn($expected);

        $result = $this->uut->addComment('10', $comment);

        self::assertSame($expected, $result);
    }

    public function testGetComment(): void
    {
        $expected = new Comment();
        $issueId = '15';
        $commentId = 231;

        $this->commentService->method('getComment')
            ->with($issueId, $commentId)
            ->willReturn($expected);

        $result = $this->uut->getComment($issueId, $commentId);

        self::assertSame($expected, $result);
    }

    public function testGetCommentsForRequest(): void
    {
        $expected = [
            new Comment(),
            new Comment(),
        ];

        $this->commentService->method('getCommentsForRequest')
            ->willReturn($expected);

        $result = $this->uut->getCommentsForRequest('10');

        self::assertSame($expected, $result);
    }

    public function testChangeAssignee(): void
    {
        $name = 'test user';

        $this->client->method('exec')
            ->with($this->uri . "/10/assignee", json_encode(['name' => $name]), 'PUT')
            ->willReturn('OK');

        $result = $this->uut->changeAssignee('10', $name);

        self::assertSame('OK', $result);
    }

    public function testChangeAssigneeByAccountId(): void
    {
        $accountId = '123';

        $this->client->method('exec')
            ->with($this->uri . "/10/assignee", json_encode(['accountId' => $accountId]), 'PUT')
            ->willReturn('OK');

        $result = $this->uut->changeAssigneeByAccountId('10', $accountId);

        self::assertSame('OK', $result);
    }

    public function testDeleteRequest(): void
    {
        $this->client->method('exec')
            ->with($this->uri . "/10?id=12", '', 'DELETE')
            ->willReturn('Deleted');

        $result = $this->uut->deleteRequest('10', ['id' => 12]);

        self::assertSame('Deleted', $result);
    }

    public function testGetTransition(): void
    {
        $item = [
            'id' => '123',
            'name' => 'test transition',
        ];

        $this->client->method('exec')
            ->with($this->uri . "/10/transitions?id=42")
            ->willReturn(json_encode(['transitions' => [$item]]));

        $result = $this->uut->getTransition('10', ['id' => 42]);

        self::assertSame($item['id'], $result[0]->id);
        self::assertSame($item['name'], $result[0]->name);
    }

    public function testFindTransitionId(): void
    {
        $items = [
            [
                'id' => '123',
                'name' => 'Transition 1',
                'to' => $this->createTransitionTo('test transition'),
            ],
            [
                'id' => '564',
                'name' => 'Transition 2',
                'to' => $this->createTransitionTo('expected transition'),
            ],
            [
                'id' => '5464',
                'name' => 'Transition 3',
                'to' => $this->createTransitionTo('last transition'),
            ]
        ];

        $this->client->method('exec')
            ->with($this->uri . "/10/transitions?")
            ->willReturn(json_encode(['transitions' => $items]));

        $result = $this->uut->findTransitionId('10', 'expected transition');

        self::assertSame($items[1]['id'], $result);
    }

    public function testGetTimeTracking(): void
    {
        $field = new IssueField();
        $field->timeTracking = new TimeTracking();
        $field->timeTracking->timeSpentSeconds = 3151231;

        $item = new Issue();
        $item->id = '1232';
        $item->key = '123vfdsfsdf';
        $item->fields = $field;

        $this->client->method('exec')
            ->with($this->uri . "/20")
            ->willReturn(json_encode($item));

        $result = $this->uut->getTimeTracking('20');

        self::assertSame($field->timeTracking->getTimeSpentSeconds(), $result->getTimeSpentSeconds());
    }

    public function testTimeTracking(): void
    {
        $expected = 'OK';
        $timeTracking = new TimeTracking();

        $data = [
            'timetracking' => [
                ['edit' => $timeTracking]
            ]
        ];

        $this->client->method('exec')
            ->with($this->uri . "/10", json_encode(['update' => $data]), 'PUT')
            ->willReturn($expected);

        $result = $this->uut->timeTracking('10', $timeTracking);

        self::assertSame($expected, $result);
    }

    public function testGetWorklog(): void
    {
        $item = [
            'startAt' => 20,
            'maxResults' => 20,
            'total' => 120,
        ];

        $this->client->method('exec')
            ->with($this->uri . "/10/worklog")
            ->willReturn(json_encode($item));

        $result = $this->uut->getWorklog('10');

        self::assertSame($item['startAt'], $result->getStartAt());
        self::assertSame($item['maxResults'], $result->getMaxResults());
        self::assertSame($item['total'], $result->getTotal());
    }

    public function testGetWorklogById(): void
    {
        $item = new stdClass();
        $item->id = 25;
        $item->timeSpent = '2 hours';

        $this->client->method('exec')
            ->with($this->uri . "/10/worklog/25")
            ->willReturn(json_encode($item));

        $result = $this->uut->getWorklogById('10', 25);

        self::assertSame($item->id, $result->id);
        self::assertSame($item->timeSpent, $result->timeSpent);
    }

	public function testGetWorklogsByIds(): void
	{
		$item1 = new stdClass();
		$item1->id = 25;
		$item1->timeSpent = '2 hours';

		$item2 = new stdClass();
		$item2->id = 50;
		$item2->timeSpent = '2 hours';


		$items = [
			$item1,
			$item2,
		];

		$this->client->method('exec')
			->with("/worklog/list", json_encode(['ids' => [25, 50]]), 'POST')
			->willReturn(json_encode($items));

		$result = $this->uut->getWorklogsByIds([25, 50]);

		self::assertSame(2, count($result));
		self::assertSame($item1->timeSpent, $result[0]->timeSpent);

	}

    public function testAddWorklog(): void
    {
        $item = $this->createWorkflow(25, '2 hours');

        $expected = $item;
        $expected->updated = 'now';

        $this->client->method('exec')
            ->with($this->uri . '/10/worklog', json_encode($item), 'POST')
            ->willReturn(json_encode($expected));

        $result = $this->uut->addWorklog('10', $item);

        self::assertSame($expected->id, $result->id);
        self::assertSame($expected->timeSpent, $result->timeSpent);
        self::assertSame($expected->updated, $result->updated);
    }

    public function testEditWorklog(): void
    {
        $item = $this->createWorkflow(20, '3 hours');

        $expected = $item;
        $expected->updated = 'now';

        $this->client->method('exec')
            ->with($this->uri . '/10/worklog/20', json_encode($item), 'PUT')
            ->willReturn(json_encode($expected));

        $result = $this->uut->editWorklog('10', $item, $item->id);

        self::assertSame($expected->id, $result->id);
        self::assertSame($expected->timeSpent, $result->timeSpent);
        self::assertSame($expected->updated, $result->updated);
    }

    public function testDeleteWorklog(): void
    {
        $this->client->method('exec')
            ->with($this->uri . '/10/worklog/20', null, 'DELETE')
            ->willReturn('1');

        $result = $this->uut->deleteWorklog('10', 20);

        self::assertTrue($result);
    }

    public function testGetAllPriorities(): void
    {
        $item = [
            'id' => '12',
            'name' => 'high',
        ];

        $this->client->method('exec')
            ->with('priority')
            ->willReturn(json_encode([$item]));

        $result = $this->uut->getAllPriorities();

        self::assertCount(1, $result);
        self::assertSame($item['id'], $result[0]->id);
        self::assertSame($item['name'], $result[0]->name);
    }

    public function testGetPriority(): void
    {
        $priorityId = 153;
        $item = [
            'id' => (string)$priorityId,
            'name' => 'medium',
        ];

        $this->client->method('exec')
            ->with("priority/$priorityId")
            ->willReturn(json_encode($item));

        $result = $this->uut->getPriority($priorityId);

        self::assertSame($item['id'], $result->id);
        self::assertSame($item['name'], $result->name);
    }

    public function testGetWatchers(): void
    {
        $item = [
            'accountId' => '213',
        ];

        $this->client->method('exec')
            ->with($this->uri . '/20/watchers')
            ->willReturn(json_encode(['watchers' => [$item]]));

        $result = $this->uut->getWatchers('20');

        self::assertCount(1, $result);
        self::assertSame($item['accountId'], $result[0]->accountId);
    }

    public function testAddWatcher(): void
    {
        $item = 'test watcher';

        $this->client->expects($this->once())
            ->method('exec')
            ->with($this->uri . '/20/watchers', json_encode($item), 'POST');
        $this->client->method('getHttpResponse')->willReturn(204);

        $result = $this->uut->addWatcher('20', $item);

        self::assertTrue($result);
    }

    public function testRemoveWatcher(): void
    {
        $watcher = 'test watcher';

        $this->client->method('exec')
            ->with($this->uri . "/20/watchers/?username=$watcher", '', 'DELETE');
        $this->client->method('getHttpResponse')->willReturn(204);

        $result = $this->uut->removeWatcher('20', $watcher);

        self::assertTrue($result);
    }

    public function testRemoveWatcherByAccountId(): void
    {
        $this->client->method('exec')
            ->with($this->uri . "/20/watchers/?accountId=123", '', 'DELETE');
        $this->client->method('getHttpResponse')->willReturn(204);

        $result = $this->uut->removeWatcherByAccountId('20', '123');

        self::assertTrue($result);
    }

    public function testGetCreateMeta(): void
    {
        $data = [
            'extra_field' => 'extra value',
        ];

        $this->client->method('exec')
            ->with($this->uri . '/createmeta?expand=projects.issuetypes.fields')
            ->willReturn(json_encode($data));

        $result = $this->uut->getCreateMeta();

        self::assertSame($data['extra_field'], $result->extra_field);
    }

    public function testGetEditMeta(): void
    {
        $key = 'test_field';
        $data = [
            'names' => 'test value 1',
            'customfield_' . $key => 'test value',
            'customfield_2' => 'test value 3',
            'ages' => 'test value 2',
        ];
        $url = $this->uri . '/' . $key . '/editmeta?overrideEditableFlag=0&overrideScreenSecurity=0';

        $this->client->method('exec')
            ->with($url)
            ->willReturn(json_encode(['fields' => $data]));

        $result = $this->uut->getEditMeta($key);
        $expected = array_splice($data, 1, 2);

        self::assertSame($expected, $result);
    }

    public function testNotify(): void
    {
        $notify = new Notify();
        $notify->htmlBody = 'Notify nothing';

        $this->client->expects($this->once())
            ->method('exec')
            ->with($this->uri . '/20/notify', json_encode($notify), 'POST')
            ->willReturn(true);

        $this->uut->notify('20', $notify);
    }

    public function testGetRemoteIssueLink(): void
    {
        $items = [
            [
                'id' => 1231,
                'relationship' => 'owner',
            ],
            [
                'id' => 76,
                'relationship' => 'owner',
            ],
        ];

        $this->client->method('exec')
            ->with($this->uri . '/20/remotelink', null)
            ->willReturn(json_encode($items));

        $result = $this->uut->getRemoteIssueLink('20');

        self::assertCount(2, $result);
        self::assertSame($items[0]['id'], $result[0]->id);
        self::assertSame($items[0]['relationship'], $result[0]->relationship);
        self::assertSame($items[1]['id'], $result[1]->id);
        self::assertSame($items[1]['relationship'], $result[1]->relationship);
    }

    public function testCreateOrUpdateRemoteIssueLink(): void
    {
        $data = new RemoteIssueLink();
        $data->id = 1231;

        $item = new RemoteIssueLink();
        $item->id = $data->id;

        $this->client->method('exec')
            ->with($this->uri . '/20/remotelink', json_encode($data), 'POST')
            ->willReturn(json_encode($item));

        $result = $this->uut->createOrUpdateRemoteIssueLink('20', $item);

        self::assertEquals($item, $result);
    }

    public function testRemoveRemoteIssueLink(): void
    {
        $this->client->expects($this->once())
            ->method('exec')
            ->with($this->uri . '/20/remotelink?globalId=121', '', 'DELETE')
            ->willReturn('OK');

        $this->uut->removeRemoteIssueLink('20', '121');
    }

    public function testGetAllIssueSecuritySchemes(): void
    {
        $items = [
            [
                'id' => 12323,
                'description' => 'allowed links',
            ],
            [
                'id' => 574,
                'description' => 'allowed users',
            ],
        ];

        $this->client->method('exec')
            ->with('/issuesecurityschemes')
            ->willReturn(json_encode(['issueSecuritySchemes' => $items]));

        $result = $this->uut->getAllIssueSecuritySchemes();

        self::assertCount(2, $result);
        self::assertSame($items[0]['id'], $result[0]->id);
        self::assertSame($items[0]['description'], $result[0]->description);
        self::assertSame($items[1]['id'], $result[1]->id);
        self::assertSame($items[1]['description'], $result[1]->description);
    }

    public function testGetIssueSecuritySchemes(): void
    {
        $data = [
            'id' => 25,
            'description' => 'test scheme',
        ];

        $this->client->method('exec')
            ->with('/issuesecurityschemes/25')
            ->willReturn(json_encode($data));

        $result = $this->uut->getIssueSecuritySchemes(25);

        self::assertSame($data['id'], $result->id);
        self::assertSame($data['description'], $result->description);
    }

    public function testUpdateLabels(): void
    {
        $labels = [
            'labels' => [
                ['add' => 'value A'],
            ]
        ];

        $this->client
            ->expects($this->once())
            ->method('exec')
            ->with($this->uri . "/20?notifyUsers=1", json_encode(['update' => $labels]), 'PUT')
            ->willReturn(true);

        $this->uut->updateLabels('20', ['value A'], []);
    }

    private function createTransitionTo(string $name): TransitionTo
    {
        $transition = new TransitionTo();
        $transition->name = $name;
        $transition->self = '';
        $transition->iconUrl = '';
        $transition->id = '';
        $transition->statusCategory = [];

        return $transition;
    }

    private function createWorkflow(int $id, string $timeSpent): Worklog
    {
        $item = new Worklog();
        $item->self = '';
        $item->author = [];
        $item->updateAuthor = [];
        $item->comment = '';
        $item->started = '';
        $item->id = $id;
        $item->timeSpent = $timeSpent;
        $item->timeSpentSeconds = 7200;
        $item->visibility = new Visibility();

        return $item;
    }
}
