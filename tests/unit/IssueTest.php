<?php

namespace JiraRestApi\Tests;

use GuzzleHttp\Psr7\Response;
use JiraRestApi\Issue\Attachment;
use JiraRestApi\Issue\Comment;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\IssueSearchResult;
use JiraRestApi\Issue\TimeTracking;
use JiraRestApi\Issue\Transition;
use JiraRestApi\Issue\Worklog;
use JiraRestApi\JiraClientResponse;
use JiraRestApi\Issue\Issue;
use JiraRestApi\Issue\IssueService;
use Psr\Http\Message\RequestInterface;

/**
 * Class IssueTest
 * @package JiraRestApi\Tests
 */
class IssueTest extends MockGuzzleClient
{
    public function testGetIssue()
    {
        $response = $this->getLocalResponse('issue.get.json');
        /** @var IssueService $issueService */
        $issueService = $this->app['jira.rest.issue'];
        $this->mockHandler->append(new Response(200, [], $response));

        $result = $issueService->get('EX-1');

        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(Issue::class, $result);
    }

    public function testSearchIssue()
    {
        $response = $this->getLocalResponse('search.json');
        /** @var IssueService $issueService */
        $issueService = $this->app['jira.rest.issue'];
        $this->mockHandler->append(new Response(200, [], $response));

        $result = $issueService->search('(project in ("EX"))');

        /** @var RequestInterface $request */
        $request = $this->mockHandler->getLastRequest();
        $body = json_decode($request->getBody()->getContents(), true);
        $this->assertInternalType('array', $body);

        $expected = json_decode('{"jql":"(project in (\"EX\"))","startAt":0,"maxResults":15,"fields":[]}', true);
        $this->assertEquals($expected, $body);

        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(IssueSearchResult::class, $result);
    }

    public function testGettingTransition()
    {
        $response = $this->getLocalResponse('transition.json');
        /** @var IssueService $issueService */
        $issueService = $this->app['jira.rest.issue'];
        $this->mockHandler->append(new Response(200, [], $response));

        $result = $issueService->getTransition('EX-1');

        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(\ArrayObject::class, $result);
        $this->assertInstanceOf(Transition::class, $result->offsetGet(0));
    }

    public function testFindTransitionId()
    {
        $response = $this->getLocalResponse('transition.json');
        /** @var IssueService $issueService */
        $issueService = $this->app['jira.rest.issue'];
        $this->mockHandler->append(
            new Response(200, [], $response),
            new Response(200, [], $response)
        );

        $result = $issueService->findTransitionId('EX-1', 'Closed');
        $this->assertEquals(711, $result);

        $this->expectException('\JiraRestApi\JiraException');
        $this->expectExceptionMessage('Transition name \'Undefined\' not found on JIRA Server.');
        $this->mockHandler->append();
        $result = $issueService->findTransitionId('EX-1', 'Undefined');

        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(JiraClientResponse::class, $result);
    }

    public function testDoTransition()
    {
        $response = $this->getLocalResponse('transition.json');
        /** @var IssueService $issueService */
        $issueService = $this->app['jira.rest.issue'];
        $this->mockHandler
            ->append(
                new Response(200, [], $response),
                new Response(204, [])
            );

        $transition = new Transition();
        $transition->setTransitionName('Closed');
        $transition->setCommentBody('Issue close by REST API.');

        $result = $issueService->doTransition('EX-1', $transition);

        /** @var RequestInterface $request */
        $request = $this->mockHandler->getLastRequest();
        $body = json_decode($request->getBody()->getContents(), true);

        $this->assertInternalType('array', $body);

        $expected = json_decode('{"transition":{"name":"Closed","id":"711"},"update":{"comment":[{"add":{"body":"Issue close by REST API."}}]}}', true);
        $this->assertEquals($expected, $body);

        $this->assertInternalType('object', $result);
        $this->assertFalse($result->hasErrors());
        $this->assertEquals(204, $result->getCode());
        $this->assertInstanceOf(JiraClientResponse::class, $result);
    }

    public function testGetTimeTracking()
    {
        $response = $this->getLocalResponse('issue.get.json');
        /** @var IssueService $issueService */
        $issueService = $this->app['jira.rest.issue'];
        $this->mockHandler->append(new Response(200, [], $response));

        $result = $issueService->getTimeTracking('EX-1');

        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(TimeTracking::class, $result);
    }

    public function testSetTimeTracking()
    {
        /** @var IssueService $issueService */
        $issueService = $this->app['jira.rest.issue'];
        $this->mockHandler->append(new Response(204, []));

        $timeTracking = new TimeTracking();
        $timeTracking->setOriginalEstimate('3w 4d 6h');
        $timeTracking->setRemainingEstimate('1w 2d 3h');

        $result = $issueService->setTimeTracking('EX-1', $timeTracking);

        /** @var RequestInterface $request */
        $request = $this->mockHandler->getLastRequest();
        $body = json_decode($request->getBody()->getContents(), true);
        $this->assertInternalType('array', $body);

        $expected = json_decode('{"update":{"timetracking":{"edit":{"originalEstimate":"3w 4d 6h","remainingEstimate":"1w 2d 3h"}}}}', true);
        $this->assertEquals($expected, $body);

        $this->assertInternalType('object', $result);
        $this->assertEquals(204, $result->getCode());
        $this->assertInstanceOf(JiraClientResponse::class, $result);
    }

    public function testGetWorklog()
    {
        $response = $this->getLocalResponse('worklog.json');
        /** @var IssueService $issueService */
        $issueService = $this->app['jira.rest.issue'];
        $this->mockHandler->append(new Response(200, [], $response));

        $result = $issueService->getWorklog('EX-1');

        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(Worklog::class, $result);
    }

    public function testCreateIssue()
    {
        $response = $this->getLocalResponse('issue.create.json');
        /** @var IssueService $issueService */
        $issueService = $this->app['jira.rest.issue'];
        $this->mockHandler->append(new Response(201, [], $response));

        $issueField = new IssueField();
        $issueField->setProjectKey('EX')
                    ->setSummary('Summary')
                    ->setDescription('Description')
                    ->setAssigneeName('assignee')
                    ->setReporterName('reporter')
                    ->setPriorityName('Critical');

        $result = $issueService->create($issueField);

        /** @var RequestInterface $request */
        $request = $this->mockHandler->getLastRequest();
        $body = json_decode($request->getBody()->getContents(), true);
        $this->assertInternalType('array', $body);

        $expected = json_decode('{"fields":{"summary":"Summary","reporter":{"name":"reporter"},"description":"Description","priority":{"name":"Critical"},"project":{"key":"EX"},"assignee":{"name":"assignee"}}}', true);
        $this->assertEquals($expected, $body);

        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(Issue::class, $result);
    }

    public function testUpdateIssue()
    {
        /** @var IssueService $issueService */
        $issueService = $this->app['jira.rest.issue'];
        $this->mockHandler->append(new Response(204, []));

        $issueField = new IssueField(true);
        $issueField->setProjectKey('EX-12')
            ->setSummary('Summary')
            ->setDescription('Description')
            ->setAssigneeName('assignee')
            ->setReporterName('reporter')
            ->setPriorityName('Critical')
            ->addLabel("test-label-first");

        $result = $issueService->update('EX-12', $issueField);

        /** @var RequestInterface $request */
        $request = $this->mockHandler->getLastRequest();
        $body = json_decode($request->getBody()->getContents(), true);
        $this->assertInternalType('array', $body);

        $expected = json_decode('{"fields":{"summary":"Summary","reporter":{"name":"reporter"},"description":"Description","priority":{"name":"Critical"},"labels":["test-label-first"],"project":{"key":"EX-12"},"assignee":{"name":"assignee"}}}', true);
        $this->assertEquals($expected, $body);

        $this->assertInternalType('object', $result);
        $this->assertFalse($result->hasErrors());
        $this->assertEquals(204, $result->getCode());
        $this->assertInstanceOf(JiraClientResponse::class, $result);
    }

    public function testAddComment()
    {
        $response = $this->getLocalResponse('issue.comment.add.json');
        /** @var IssueService $issueService */
        $issueService = $this->app['jira.rest.issue'];
        $this->mockHandler->append(new Response(201, [], $response));

        $comment = new Comment();
        $comment
            ->setBody('some comment body')
            ->setVisibility('role', 'Users');

        $result = $issueService->addComment('EX-12', $comment);

        /** @var RequestInterface $request */
        $request = $this->mockHandler->getLastRequest();
        $body = json_decode($request->getBody()->getContents(), true);

        $this->assertInternalType('array', $body);
        $expected = json_decode('{"body":"some comment body","visibility":{"type":"role","value":"Users"}}', true);
        $this->assertEquals($expected, $body);

        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(Comment::class, $result);
    }

    public function testUpdateComment()
    {
        $response = $this->getLocalResponse('issue.comment.add.json');
        /** @var IssueService $issueService */
        $issueService = $this->app['jira.rest.issue'];
        $this->mockHandler->append(new Response(200, [], $response));

        $comment = new Comment();
        $comment
            ->setId('10000')
            ->setBody('some comment body')
            ->setVisibility('role', 'Administrators');

        $result = $issueService->updateComment('EX-12', $comment);

        /** @var RequestInterface $request */
        $request = $this->mockHandler->getLastRequest();
        $body = json_decode($request->getBody()->getContents(), true);

        $this->assertInternalType('array', $body);
        $expected = json_decode('{"body":"some comment body","visibility":{"type":"role","value":"Administrators"}}', true);
        $this->assertEquals($expected, $body);

        $this->assertInternalType('object', $result);
        $this->assertInstanceOf(Comment::class, $result);

        $comment = new Comment();
        $comment
            ->setBody('some comment body')
            ->setVisibility('role', 'Administrators');

        $this->expectException('\JiraRestApi\JiraException');
        $this->expectExceptionMessage('CommentId not found in Comment object.');
        $issueService->updateComment('EX-12', $comment);
    }

    public function testSetlabel()
    {
        /** @var IssueService $issueService */
        $issueService = $this->app['jira.rest.issue'];
        $this->mockHandler->append(new Response(204, []));

        $result = $issueService->setLabel('EX-12', 'hello');

        /** @var RequestInterface $request */
        $request = $this->mockHandler->getLastRequest();
        $body = json_decode($request->getBody()->getContents(), true);

        $this->assertInternalType('array', $body);
        $expected = json_decode('{"update":{"labels":[{"set":["hello"]}]}}', true);
        $this->assertEquals($expected, $body);

        $this->assertInternalType('object', $result);
        $this->assertEquals(204, $result->getCode());
        $this->assertInstanceOf(JiraClientResponse::class, $result);
    }

    public function testRemovelabel()
    {
        /** @var IssueService $issueService */
        $issueService = $this->app['jira.rest.issue'];
        $this->mockHandler->append(new Response(204, []));

        $result = $issueService->removeLabel('EX-12', 'hello');

        /** @var RequestInterface $request */
        $request = $this->mockHandler->getLastRequest();
        $body = json_decode($request->getBody()->getContents(), true);

        $this->assertInternalType('array', $body);
        $expected = json_decode('{"update":{"labels":[{"remove":["hello"]}]}}', true);
        $this->assertEquals($expected, $body);

        $this->assertInternalType('object', $result);
        $this->assertEquals(204, $result->getCode());
        $this->assertInstanceOf(JiraClientResponse::class, $result);
    }

    public function testAddAttachments()
    {
        $pathToFiles = __DIR__ . '/../fixtures/files/';
        $response = $this->getLocalResponse('upload.json');
        $response2 = $this->getLocalResponse('error.json');
        /** @var IssueService $issueService */
        $issueService = $this->app['jira.rest.issue'];
        $this->mockHandler->append(
            new Response(200, [], $response),
            new Response(200, [], $response),
            new Response(404, [], $response2)
        );

        $results = $issueService->addAttachments('EX-12', [
            $pathToFiles.'test.txt',
            $pathToFiles.'test2.txt',
            $pathToFiles.'test3.txt'
        ]);

        $this->assertInternalType('array', $results);
        $this->assertEquals(3, sizeof($results));

        $first = reset($results);
        $this->assertInstanceOf(\ArrayObject::class, $first);
        $this->assertEquals(2, sizeof($first));

        $item = $first->offsetGet(0);
        $this->assertInstanceOf(Attachment::class, $item);

        $last = end($results);
        $this->assertInstanceOf(JiraClientResponse::class, $last);
        $this->assertEquals(404, $last->getCode());
    }
}