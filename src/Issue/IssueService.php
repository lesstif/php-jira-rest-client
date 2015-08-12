<?php

namespace JiraRestApi\Issue;

class IssueService extends \JiraRestApi\JiraClient
{
    private $uri = '/issue';

    public function __construct(Array $config)
    {
        parent::__construct($config);
    }

    /**
     * get all project list.
     *
     * @return Issue class
     */
    public function get($issueIdOrKey)
    {
        $ret = $this->exec($this->uri."/$issueIdOrKey", null);

        $this->log->addInfo("Result=\n".$ret);

        $issue = $this->json_mapper->map(
             json_decode($ret), new Issue()
        );

        return $issue;
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

        $issue = $this->json_mapper->map(
             json_decode($ret), new Issue()
        );

        return $issue;
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
        $results = $this->upload($this->uri."/$issueIdOrKey/attachments", $filePathArray);

        $this->log->addInfo('addAttachments result='.var_export($results, true));

        $resArr = array();
        foreach ($results as $ret) {
            array_push($resArr, $this->json_mapper->mapArray(
               json_decode($ret), new \ArrayObject(), '\JiraRestApi\Issue\Attachment'
                )
            );
        }

        return $resArr;
    }

    /**
     * update issue.
     *
     * @param   $issueIdOrKey Issue Key
     * @param   $issueField   object of Issue class
     *
     * @return created issue key
     */
    public function update($issueIdOrKey, $issueField)
    {
        $issue = new Issue();

        // serilize only not null field.
        $issue->fields = $issueField;

        //$issue = $this->filterNullVariable((array)$issue);

        $data = json_encode($issue);

        $this->log->addInfo("Update Issue=\n".$data);

        $ret = $this->exec($this->uri."/$issueIdOrKey", $data, 'PUT');

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

        return;
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
     * Search issues
     *
     * @param       $jql
     * @param int   $startAt
     * @param int   $maxResults
     * @param array $fields
     *
     * @return IssueSearchResult
     */
    public function search($jql, $startAt=0, $maxResults=15, $fields=[])
    {
        $data = json_encode(array(
            'jql' => $jql,
            'startAt' => $startAt,
            'maxResults' => $maxResults,
            'fields' => $fields
        ));

        $ret = $this->exec("search", $data, 'POST');

        $result = $this->json_mapper->map(
            json_decode($ret), new IssueSearchResult()
        );

        return $result;
    }

    /**
     * get worklog info
     * 
     * @param type $issueIdOrKey 
     * @return type @TimeTracking
     */
    public function getWorklog($issueIdOrKey)
    {
        $ret = $this->exec($this->uri . "/$issueIdOrKey", null);
        $this->log->addDebug("getWorklog res=$ret\n");

        $issue = $this->json_mapper->map(
             json_decode($ret), new Issue()
        );

        return $issue->fields->timetracking;
    }

     /**
     * worklog issues
     *
     * @param issueIdOrKey Issue id or key
     * @param timeTracking   TimeTracking
     *
     * @return TimeTracking
     */
    public function worklog($issueIdOrKey, $timeTracking)
    {   
        $array = ["update" =>
            [
                "timetracking" => [
                    ["edit" => $timeTracking]
                ]
            ]
        ];

        $data = json_encode($array);

        $this->log->addDebug("worklog req=$data\n");

        $ret = $this->exec($this->uri . "/$issueIdOrKey", $data, 'PUT');   

        // FIXME 
        $result = $this->json_mapper->map(
            json_decode($ret), new TimeTracking()
        );

        return $result;
    }
}

?>

