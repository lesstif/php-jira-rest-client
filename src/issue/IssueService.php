<?php

namespace JiraRestApi\Issue;

require 'vendor/autoload.php';

class IssueService extends \JiraRestApi\JiraClient {
    private $uri = "/issue";

 	public function __construct() {
        parent::__construct(getHostConfig(), getOptions());        
    }

    /**
     * get all project list
     * 
     * @return Issue class
     */
    public function get($issueIdOrKey) {
    	$ret = $this->exec("$this->uri/$issueIdOrKey", null);

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
        $issue->fields = array_filter((array) $issueField, function ($val) {
            return !is_null($val);
        });

        $data = json_encode($issue);

        $this->log->addInfo("Create Issue=\n" . $data );

        $ret = $this->exec($this->uri, $data, "POST");

        $issue = $this->json_mapper->map(
             json_decode($ret), new Issue()
        );

        return $issue;
    }
}

?>

