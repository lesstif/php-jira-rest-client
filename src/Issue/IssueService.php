<?php

namespace JiraRestApi\Issue;

use JiraRestApi\JiraException;

class IssueService extends \JiraRestApi\JiraClient
{
    private $uri = '/issue';

    public function getIssueFromJSON($json)
    {
        $issue = $this->json_mapper->map(
            $json, new Issue()
        );

        return $issue;
    }

    /**
     *  get all project list.
     *
     * @param $issueIdOrKey
     * @param array $paramArray Query Parameter key-value Array.
     * @param Issue $issueObject
     *
     * @return Issue class
     *
     * @throws JiraException
     * @throws \JsonMapper_Exception
     */
    public function get($issueIdOrKey, $paramArray = [], $issueObject = null)
    {
        $issueObject = ($issueObject) ? $issueObject : new Issue();

        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->exec($this->uri.'/'.$issueIdOrKey.$queryParam, null);

        $this->log->addInfo("Result=\n".$ret);

        return $issue = $this->json_mapper->map(
            json_decode($ret), $issueObject
        );
    }

    /**
     * create new issue.
     *
     * @param   $issue object of Issue class
     *
     * @return created issue key
     */
    public function create($issueField)
    {
        $issue = new Issue();

        // serilize only not null field.
        $issue->fields = $issueField;

        $data = json_encode($issue);

        $this->log->addInfo("Create Issue=\n".$data);

        $ret = $this->exec($this->uri, $data, 'POST');

        return $this->getIssueFromJSON(json_decode($ret));
    }

    /**
     * Create multiple issues using bulk insert.
     * 
     * @param IssueField[] $issueFields Array of IssueField objects
     * @param int          $batchSize   Maximum number of issues to send in each request
     * 
     * @return [] Array of results, where each result represents one batch of insertions
     */
    public function createMultiple($issueFields, $batchSize = 50)
    {
        $issues = [];

        foreach ($issueFields as $issueField) {
            $issue = new Issue();
            $issue->fields = $issueField;
            $issues[] = $issue;
        }

        $batches = array_chunk($issues, $batchSize);

        $results = [];
        foreach ($batches as $batch) {
            $results = array_merge($results, $this->bulkInsert($batch));
        }

        return $results;
    }

    /**
     * Makes API call to bulk insert issues.
     * 
     * @param [] $issues Array of issue arrays that are sent to Jira one by one in single create
     * 
     * @return [] Result of API call to insert many issues
     */
    private function bulkInsert($issues)
    {
        $data = json_encode(['issueUpdates' => $issues]);

        $this->log->addInfo("Create Issues=\n".$data);
        $results = $this->exec($this->uri.'/bulk', $data, 'POST');

        $issues = [];
        foreach (json_decode($results)->issues as $result) {
            $issues[] = $this->getIssueFromJSON($result);
        }

        return $issues;
    }

    /**
     * Add one or more file to an issue.
     *
     * @param issueIdOrKey Issue id or key
     * @param filePathArray attachment file path.
     *
     * @return
     */
    public function addAttachments($issueIdOrKey, $filePathArray)
    {
        if (is_array($filePathArray) == false) {
            $filePathArray = [$filePathArray];
        }

        $results = $this->upload($this->uri."/$issueIdOrKey/attachments", $filePathArray);

        $this->log->addInfo('addAttachments result='.var_export($results, true));

        $resArr = array();
        foreach ($results as $ret) {
            $ret = json_decode($ret);
            if (is_array($ret)) {
                array_push($resArr, $this->json_mapper->mapArray(
                    $ret, new \ArrayObject(), '\JiraRestApi\Issue\Attachment'
                    )
                );
            } elseif (is_object($ret)) {
                array_push($resArr, $this->json_mapper->map(
                    $ret, new Attachment
                    )
                );
            }
        }

        return $resArr;
    }

    /**
     * update issue.
     *
     * @param   $issueIdOrKey Issue Key
     * @param   $issueField   object of Issue class
     * @param array $paramArray Query Parameter key-value Array.
     *
     * @return created issue key
     */
    public function update($issueIdOrKey, $issueField, $paramArray = [])
    {
        $issue = new Issue();

        // serilize only not null field.
        $issue->fields = $issueField;

        //$issue = $this->filterNullVariable((array)$issue);

        $data = json_encode($issue);

        $this->log->addInfo("Update Issue=\n".$data);

        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->exec($this->uri."/$issueIdOrKey".$queryParam, $data, 'PUT');

        return $ret;
    }

    /**
     * Adds a new comment to an issue.
     *
     * @param issueIdOrKey Issue id or key
     * @param comment .
     *
     * @return Comment class
     */
    public function addComment($issueIdOrKey, $comment)
    {
        $this->log->addInfo("addComment=\n");

        $data = json_encode($comment);

        $ret = $this->exec($this->uri."/$issueIdOrKey/comment", $data);

        $this->log->addDebug('add comment result='.var_export($ret, true));
        $comment = $this->json_mapper->map(
           json_decode($ret), new Comment()
        );

        return $comment;
    }

    /**
     * Change a issue assignee
     *
     * @param Issue $issueIdOrKey
     * @param Assigns $assigneeName Assigns an issue to a user.
     *    If the assigneeName is "-1" automatic assignee is used.
     *    A null name will remove the assignee.
     * @return true | false
     * @throws JiraException
     *
     */
    public function changeAssignee($issueIdOrKey, $assigneeName)
    {
        $this->log->addInfo("changeAssignee=\n");

        $ar = ['name' => $assigneeName];

        $data = json_encode($ar);

        $ret = $this->exec($this->uri."/$issueIdOrKey/assignee", $data, 'PUT');

        $this->log->addInfo('change assignee of '.$issueIdOrKey.' to ' . $assigneeName .' result='.var_export($ret, true));

        return $ret;
    }

    /**
      * Delete a issue.
      *
      * @param issueIdOrKey Issue id or key
      * @param array $paramArray Query Parameter key-value Array.
      * @return true | false
      *
      */
    public function deleteIssue($issueIdOrKey, $paramArray = [])
    {
        $this->log->addInfo("deleteIssue=\n");

        $queryParam = '?'.http_build_query($paramArray);

        $ret = $this->exec($this->uri."/$issueIdOrKey".$queryParam, '', 'DELETE');

        $this->log->addInfo('delete issue '.$issueIdOrKey.' result='.var_export($ret, true));

        return $ret;
    }
    
    /**
     * Get a list of the transitions possible for this issue by the current user, along with fields that are required and their types.
     *
     * @param issueIdOrKey Issue id or key
     *
     * @return array of Transition class
     */
    public function getTransition($issueIdOrKey)
    {
        $ret = $this->exec($this->uri."/$issueIdOrKey/transitions");

        $this->log->addDebug('getTransitions result='.var_export($ret, true));

        $data = json_encode(json_decode($ret)->transitions);

        $transitions = $this->json_mapper->mapArray(
           json_decode($data), new \ArrayObject(), '\JiraRestApi\Issue\Transition'
        );

        return $transitions;
    }

    /**
     * find transition id by transition's to field name(aka 'Resolved').
     */
    public function findTransitonId($issueIdOrKey, $transitionToName)
    {
        $this->log->addDebug('findTransitonId=');

        $ret = $this->getTransition($issueIdOrKey);

        foreach ($ret as $trans) {
            $toName = $trans->to->name;

            $this->log->addDebug('getTransitions result='.var_export($ret, true));

            if (strcmp($toName, $transitionToName) == 0) {
                return $trans->id;
            }
        }

        // transition keyword not found
        throw new JiraException('Transition name \''.$transitionToName.'\' not found on JIRA Server.');
    }

    /**
     * Perform a transition on an issue.
     *
     * @param issueIdOrKey Issue id or key
     *
     * @return nothing - if transition was successful return http 204(no contents)
     */
    public function transition($issueIdOrKey, $transition)
    {
        $this->log->addDebug('transition='.var_export($transition, true));

        if (!isset($transition->transition['id'])) {
            $transition->transition['id'] = $this->findTransitonId($issueIdOrKey, $transition->transition['name']);
        }

        $data = json_encode($transition);

        $this->log->addDebug("transition req=$data\n");

        $ret = $this->exec($this->uri."/$issueIdOrKey/transitions", $data, 'POST');

        $this->log->addDebug('getTransitions result='.var_export($ret, true));
    }

    /**
     * Search issues.
     *
     * @param       $jql
     * @param int   $startAt
     * @param int   $maxResults
     * @param array $fields
     *
     * @return IssueSearchResult
     */
    public function search($jql, $startAt = 0, $maxResults = 15, $fields = [])
    {
        $data = json_encode(array(
            'jql' => $jql,
            'startAt' => $startAt,
            'maxResults' => $maxResults,
            'fields' => $fields,
        ));

        $ret = $this->exec('search', $data, 'POST');
        $json = json_decode($ret);

        $result = $this->json_mapper->map(
            $json, new IssueSearchResult()
        );

        return $result;
    }

    /**
     * get TimeTracking info.
     *
     * @param type $issueIdOrKey
     *
     * @return type @TimeTracking
     */
    public function getTimeTracking($issueIdOrKey)
    {
        $ret = $this->exec($this->uri."/$issueIdOrKey", null);
        $this->log->addDebug("getTimeTracking res=$ret\n");

        $issue = $this->json_mapper->map(
             json_decode($ret), new Issue()
        );

        return $issue->fields->timeTracking;
    }

    /**
     * TimeTracking issues.
     *
     * @param issueIdOrKey Issue id or key
     * @param timeTracking   TimeTracking
     *
     * @return type @TimeTracking
     */
    public function timeTracking($issueIdOrKey, $timeTracking)
    {
        $array = ['update' => [
                'timetracking' => [
                    ['edit' => $timeTracking],
                ],
            ],
        ];

        $data = json_encode($array);

        $this->log->addDebug("TimeTracking req=$data\n");

        // if success, just return HTTP 201.
        $ret = $this->exec($this->uri."/$issueIdOrKey", $data, 'PUT');

        return $ret;
    }

    /**
     * get getWorklog.
     *
     * @param mixed $issueIdOrKey
     *
     * @return PaginatedWorklog object
     */
    public function getWorklog($issueIdOrKey)
    {
        $ret = $this->exec($this->uri."/$issueIdOrKey/worklog");
        $this->log->addDebug("getWorklog res=$ret\n");
        $worklog = $this->json_mapper->map(
            json_decode($ret), new PaginatedWorklog()
        );

        return $worklog;
    }

    /**
     * get getWorklog by Id.
     *
     * @param mixed $issueIdOrKey
     * @param int   $workLogId
     *
     * @return PaginatedWorklog object
     */
    public function getWorklogById($issueIdOrKey, $workLogId)
    {
        $ret = $this->exec($this->uri."/$issueIdOrKey/worklog/$workLogId");
        $this->log->addDebug("getWorklogById res=$ret\n");
        $worklog = $this->json_mapper->map(
            json_decode($ret), new Worklog()
        );

        return $worklog;
    }

    /**
     * add work log to issue.
     * 
     * @param mixed  $issueIdOrKey
     * @param object $worklog
     * @param int $worklogId
     *
     * @return Worklog Object
     */
    public function addWorklog($issueIdOrKey, $worklog){
        $this->log->addInfo("addWorklog=\n");

        $data = json_encode($worklog);
        $url = $this->uri . "/$issueIdOrKey/worklog";
        $type = 'POST';

        $ret = $this->exec($url, $data, $type);

        $ret_worklog = $this->json_mapper->map(
           json_decode($ret), new Worklog()
        );

        return $ret_worklog;
    }

    /**
     * edit the worklog
     *
     * @param $issueIdOrKey
     * @param $worklog
     * @param string $worklogId
     * @return object
     * @throws JiraException
     * @throws \JsonMapper_Exception
     */
    public function editWorklog($issueIdOrKey, $worklog, $worklogId){
        $this->log->addInfo("editWorklog=\n");

        $data = json_encode($worklog);
        $url = $this->uri . "/$issueIdOrKey/worklog/$worklogId";
        $type = 'PUT';

        $ret = $this->exec($url, $data, $type);

        $ret_worklog = $this->json_mapper->map(
            json_decode($ret), new Worklog()
        );

        return $ret_worklog;
    }

    /**
     * Get all priorities.
     *
     * @return array of priority class
     */
    public function getAllPriorities()
    {
        $ret = $this->exec('priority', null);

        $priorities = $this->json_mapper->mapArray(
             json_decode($ret, false), new \ArrayObject(), '\JiraRestApi\Issue\Priority'
        );

        return $priorities;
    }

    /**
     * Get priority by id.
     *
     * @param priorityId Id of priority.
     *
     * @throws HTTPException if the priority is not found, or the calling user does not have permission or view it.
     *
     * @return string priority id
     */
    public function getPriority($priorityId)
    {
        $ret = $this->exec("priority/$priorityId", null);

        $this->log->addInfo('Result='.$ret);

        $prio = $this->json_mapper->map(
             json_decode($ret), new Priority()
        );

        return $prio;
    }

    /**
     * Get priority by id.
     *
     * @param priorityId Id of priority.
     *
     * @throws HTTPException if the priority is not found, or the calling user does not have permission or view it.
     *
     * @return string priority id
     */
    public function getCustomFields($priorityId)
    {
        $ret = $this->exec("priority/$priorityId", null);

        $this->log->addInfo('Result='.$ret);

        $prio = $this->json_mapper->map(
            json_decode($ret), new Priority()
        );

        return $prio;
    }

    /**
    * add watcher to issue.
    *
    * @param mixed  $issueIdOrKey
    * @param object $watcher
    * @param int $worklogId
    *
    * @return bool
    */
    public function addWatcher($issueIdOrKey, $watcher)
    {
        $this->log->addInfo("addWatcher=\n");

        $data = json_encode($watcher);
        $url = $this->uri . "/$issueIdOrKey/watchers";
        $type = 'POST';

        $this->exec($url, $data, $type);

        return $this->http_response == 204 ? true : false;
    }

}
