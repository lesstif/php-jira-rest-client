<?php

namespace JiraRestApi\Issue;

class IssueService extends \JiraRestApi\JiraClient {
    private $uri = "/issue";

 	public function __construct() {
        parent::__construct();        
    }

    /**
     * get all project list
     * 
     * @return Issue class
     */
    public function get($issueIdOrKey) {
    	$ret = $this->exec($this->uri . "/$issueIdOrKey", null);

        $this->log->addInfo("Result=\n" . $ret );

        $issue = $this->json_mapper->map(
             json_decode($ret), new Issue()
        );

        return $issue;
    }

    /**
     * create new issue
     * 
     * @param   $issue object of Issue class
     * 
     * @return created issue key
     */
    public function create($issueField) {
        $issue = new Issue();

        // serilize only not null field.
        $issue->fields = $issueField;

        $data = json_encode($issue);

        $this->log->addInfo("Create Issue=\n" . $data );

        $ret = $this->exec($this->uri, $data, "POST");

        $issue = $this->json_mapper->map(
             json_decode($ret), new Issue()
        );

        return $issue;
    }

    /**
     * Add one or more file to an issue
     * 
     * @param issueIdOrKey Issue id or key
     * @param filePath attachment file.
     * 
     * @return
     */
    public function addAttachments($issueIdOrKey, $filePath) {
       
        $this->log->addInfo("addAttachments=\n");

        $ret = $this->upload($this->uri . "/$issueIdOrKey/attachments", $filePath);

        $issue = $this->json_mapper->mapArray(
           json_decode($ret), new \ArrayObject(), '\JiraRestApi\Issue\Attachment'
        );

        return $issue;
    }

    /**
     * update issue
     * 
     * @param   $issueIdOrKey Issue Key
     * @param   $issueField object of Issue class
     * 
     * @return created issue key
     */
    public function update($issueIdOrKey, $issueField) {
        $issue = new Issue();

        // serilize only not null field.
        $issue->fields = $issueField;

        //$issue = $this->filterNullVariable((array)$issue);

        $data = json_encode($issue);

        $this->log->addInfo("Update Issue=\n" . $data );

        $ret = $this->exec($this->uri . "/$issueIdOrKey", $data, "PUT");

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
    public function addComment($issueIdOrKey, $comment) {
       
        $this->log->addInfo("addComment=\n");

        $data = json_encode($comment);
        
        $ret = $this->exec($this->uri . "/$issueIdOrKey/comment", $data);

        $this->log->addDebug("add comment result=" . var_export($ret, true));
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
    public function getTransition($issueIdOrKey) {
       
        $ret = $this->exec($this->uri . "/$issueIdOrKey/transitions");

        $this->log->addDebug("getTransitions result=" . var_export($ret, true));

        $data = json_encode(json_decode($ret)->transitions);

        $transitions = $this->json_mapper->mapArray(
           json_decode($data), new \ArrayObject(), '\JiraRestApi\Issue\Transition'
        );

        return $transitions;
    }

    /**
     * find transition id by transition's to field name(aka 'Resolved')
     * 
     */ 
    public function findTransitonId($issueIdOrKey, $transitionToName) {
        $this->log->addDebug("findTransitonId=");

        $ret = $this->getTransition($issueIdOrKey);
        
        foreach($ret as $trans) {
            $toName = $trans->to->name;
            
             $this->log->addDebug("getTransitions result=" . var_export($ret, true));

            if (strcmp($toName, $transitionToName) == 0){
                return $trans->id;
            }
        }

        return null;
    } 

    /**
     * Perform a transition on an issue.
     * 
     * @param issueIdOrKey Issue id or key
     * 
     * @return nothing - if transition was successful return http 204(no contents)
     */
    public function transition($issueIdOrKey, $transition) {
        $this->log->addDebug("transition=" . var_export($transition, true));

        if (!isset($transition->transition['id'])) {
            $transition->transition['id'] = $this->findTransitonId($issueIdOrKey, $transition->transition['name']);
        }

        $data = json_encode($transition);

        $this->log->addDebug("transition req=$data\n");

        $ret = $this->exec($this->uri . "/$issueIdOrKey/transitions", $data, "POST");

        $this->log->addDebug("getTransitions result=" . var_export($ret, true));
    }
}

?>

