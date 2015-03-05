<?php

namespace JiraRestApi\Issue;

class IssueService extends \JiraRestApi\JiraClient {
    private $uri = "/issue";

 	public function __construct() {
        parent::__construct(getConfig());        
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
}

?>

