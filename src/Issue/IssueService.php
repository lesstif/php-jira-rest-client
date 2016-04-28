<?php

namespace JiraRestApi\Issue;

use JiraRestApi\JiraClient;
use JiraRestApi\JiraClientResponse;
use JiraRestApi\JiraException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

class IssueService extends JiraClient
{
    private $uri = '/issue';
    private $searchUri = '/search';

    /**
     * Get issue
     *
     * @param $issueIdOrKey Issue id or key
     *
     * @return mixed
     */
    public function get($issueIdOrKey)
    {
        $result = $this->exec($this->uri . '/' . $issueIdOrKey);

        return $this->extractErrors($result, [200], function () use ($result) {
            return $this->json_mapper->map(
                $result->getRawData(), new Issue()
            );
        });
    }

    /**
     * Search issues.
     *
     * @param       $jql
     * @param int   $startAt
     * @param int   $maxResults
     * @param array $fields
     *
     * @return mixed
     */
    public function search($jql, $startAt = 0, $maxResults = 15, $fields = [])
    {
        $data = [
            'jql' => $jql,
            'startAt' => $startAt,
            'maxResults' => $maxResults,
            'fields' => $fields,
        ];

        /** @var JiraClientResponse $result */
        $result = $this->exec($this->searchUri, $data, Request::METHOD_POST);

        return $this->extractErrors($result, [200], function () use ($result) {
            return $this->json_mapper->map(
                $result->getRawData(), new IssueSearchResult()
            );
        });
    }

    /**
     * Create new issue.
     *
     * @param IssueField $issueField
     *
     * @return mixed
     */
    public function create(IssueField $issueField)
    {
        $issue = new Issue();
        $issue->fields = $issueField;
        $data = $this->filterNullVariable($issue);

        $result = $this->exec($this->uri, $data, Request::METHOD_POST);

        return $this->extractErrors($result, [201], function () use ($result) {
            return $this->json_mapper->map(
                $result->getRawData(), new Issue()
            );
        });
    }

    /**
     * Update issue.
     *
     * @param            $issueIdOrKey Issue id or key
     * @param IssueField $issueField
     *
     * @return string
     */
    public function update($issueIdOrKey, IssueField $issueField)
    {
        $issue = new Issue();
        $issue->fields = $issueField;

        // ToDO: Do we need to filter null variables?
        $data = $this->filterNullVariable($issue);

        $result = $this->exec($this->uri . '/' . $issueIdOrKey, $data, Request::METHOD_PUT);

        return $this->extractErrors($result, [204], function () use ($result) {
            return $result;
        });
    }

    /**
     * Add one or more file to an issue.
     *
     * @param       $issueIdOrKey  Issue id or key
     * @param array $filePathArray attachment file path.
     *
     * @return array
     */
    public function addAttachments($issueIdOrKey, array $filePathArray)
    {
        $results = $this->upload($this->uri . '/' . $issueIdOrKey .'/attachments', $filePathArray);

        $resArr = [];
        foreach ($results as $result) {
            $extracted = $this->extractErrors($result, [200], function () use ($result) {
                return $this->json_mapper->mapArray(
                    $result->getRawData(), new \ArrayObject(), '\JiraRestApi\Issue\Attachment'
                );
            });

            array_push($resArr, $extracted);
        }

        return $resArr;
    }

    /**
     * Adds a new comment to an issue.
     *
     * @param         $issueIdOrKey Issue id or key
     * @param Comment $comment
     *
     * @return mixed
     */
    public function addComment($issueIdOrKey, Comment $comment)
    {
        $data = $this->filterNullVariable($comment);
        $result = $this->exec($this->uri . '/' . $issueIdOrKey . '/comment', $data, Request::METHOD_POST);

        return $this->extractErrors($result, [201], function () use ($result) {
            return $this->json_mapper->map(
                $result->getRawData(), new Comment()
            );
        });
    }

    /**
     * Update comment to an issue.
     *
     * @param         $issueIdOrKey Issue id or key
     * @param Comment $comment
     *
     * @return mixed
     * @throws JiraException
     */
    public function updateComment($issueIdOrKey, Comment $comment)
    {
        $data = $this->filterNullVariable($comment);

        if (!isset($data['id'])) {
            throw new JiraException('CommentId not found in Comment object.');
        }

        $commentId = $data['id'];
        unset($data['id']);

        $result = $this->exec($this->uri . '/' . $issueIdOrKey . '/comment/' . $commentId, $data, Request::METHOD_PUT);

        return $this->extractErrors($result, [200], function () use ($result) {
            return $this->json_mapper->map(
                $result->getRawData(), new Comment()
            );
        });
    }

    /**
     * Get a list of the transitions possible for this
     * issue by the current user, along with fields that
     * are required and their types.
     *
     * @param $issueIdOrKey
     *
     * @return mixed|string
     */
    public function getTransition($issueIdOrKey)
    {
        $result = $this->exec($this->uri . '/' . $issueIdOrKey . '/transitions');

        return $this->extractErrors($result, [200], function () use ($result) {
            $data = $result->getRawData();
            return $this->json_mapper->mapArray(
                $data['transitions'], new \ArrayObject(), '\JiraRestApi\Issue\Transition'
            );
        });
    }

    /**
     * Find transition id by transition's to field name(aka 'Resolved').
     *
     * @param $issueIdOrKey
     * @param $transitionToName
     *
     * @return mixed
     * @throws JiraException
     */
    public function findTransitionId($issueIdOrKey, $transitionToName)
    {
        $ret = $this->getTransition($issueIdOrKey);

        foreach ($ret as $trans) {
            $toName = $trans->to->name;

            if (strcmp($toName, $transitionToName) == 0) {
                return $trans->id;
            }
        }

        // transition keyword not found
        throw new JiraException('Transition name \'' . $transitionToName . '\' not found on JIRA Server.');
    }

    /**
     * Perform a transition on an issue.
     *
     * @param            $issueIdOrKey Issue id or key
     * @param Transition $transition
     *
     * @return mixed - if transition was successful return http 204(no contents)
     * @throws JiraException
     */
    public function doTransition($issueIdOrKey, Transition $transition)
    {
        if (!isset($transition->transition['id'])) {
            $transition->transition['id'] = $this->findTransitionId($issueIdOrKey, $transition->transition['name']);
        }

        $data = $this->filterNullVariable($transition);

        $result = $this->exec($this->uri . '/' . $issueIdOrKey . '/transitions', $data, Request::METHOD_POST);

        return $this->extractErrors($result, [204], function () use ($result) {
            return $result;
        });
    }

    /**
     * Get TimeTracking info.
     *
     * @param $issueIdOrKey
     *
     * @return bool|TimeTracking
     */
    public function getTimeTracking($issueIdOrKey)
    {
        $result = $this->get($issueIdOrKey);

        if ($result instanceof Issue) {
            return $result->fields->timeTracking;
        }

        return false;
    }

    /**
     * TimeTracking issues.
     *
     * @param $issueIdOrKey
     * @param $timeTracking
     *
     * @return string
     */
    public function setTimeTracking($issueIdOrKey, TimeTracking $timeTracking)
    {
        $data['update']['timetracking']['edit'] = $timeTracking;
        $data = $this->filterNullVariable($data);

        $result = $this->exec($this->uri . '/' . $issueIdOrKey, $data, Request::METHOD_PUT);

        return $this->extractErrors($result, [204], function () use ($result) {
            return $result;
        });
    }

    /**
     * Get getWorklog.
     *
     * @param mixed $issueIdOrKey
     *
     * @return Worklog Return Worklog object
     */
    public function getWorklog($issueIdOrKey)
    {
        $result = $this->exec($this->uri . '/' . $issueIdOrKey . '/worklog');

        return $this->extractErrors($result, [200], function () use ($result) {
            return $this->json_mapper->map(
                $result->getRawData(), new Worklog()
            );
        });
    }

    /**
     * @param $issueIdOrKey
     * @param $label
     *
     * @return mixed
     */
    public function setLabel($issueIdOrKey, $label)
    {
        $labels = is_array($label) ? $label : [$label];

        $data['update']['labels'][]['set'] = $labels;

        $result = $this->exec($this->uri . '/' . $issueIdOrKey, $data, Request::METHOD_PUT);
        return $this->extractErrors($result, [204], function () use ($result) {
            return $result;
        });
    }

    /**
     * @param $issueIdOrKey
     * @param $label
     *
     * @return mixed
     */
    public function removeLabel($issueIdOrKey, $label)
    {
        $labels = is_array($label) ? $label : [$label];

        $data['update']['labels'][]['remove'] = $labels;

        $result = $this->exec($this->uri . '/' . $issueIdOrKey, $data, Request::METHOD_PUT);
        return $this->extractErrors($result, [204], function () use ($result) {
            return $result;
        });
    }
}
