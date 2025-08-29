<?php

namespace JiraRestApi\Test;

use DateInterval;
use DateTime;
use Exception;
use JiraRestApi\Epic\EpicService;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\IssueService;
use PHPUnit\Framework\TestCase;
use JiraRestApi\Dumper;
use JiraRestApi\Group\Group;
use JiraRestApi\Group\GroupService;
use JiraRestApi\JiraException;

class EpicTest extends TestCase
{
    /**
     * @test
     *
     */
    public function create_new_epic() :string
    {
        try {
            $issueField = new IssueField();

            $issueField->setProjectKey('EPICTEST')
                ->setAssigneeNameAsString('lesstif')
                ->setPriorityNameAsString('Critical')
                ->setIssueTypeAsString('Epic')
                // some jira instance need localization Epic name like below.
                //->setIssueTypeAsString('큰틀')

                // Create Epic always need epic names custom field,
                // unfortunately is custom field name different each systems.
                ->addCustomField('customfield_10403', 'My Big Epic name')
                ->setSummary("My Big Epic") // Don't confused epic name with summary.
            ;

            $issueService = new IssueService();

            $ret = $issueService->create($issueField);

            //If success, Returns a link to the created issue.
            print_r($ret);

            $issueKey = $ret->{'key'};

            $this->assertNotNull($issueKey);

            return $issueKey;
        } catch (Exception $e) {
            $this->fail('create_new_epic : ' . $e->getMessage());
        }
    }

    /**
     * @test
     *
     * @depends create_new_epic
     *
     * @return string|void
     * @throws \JsonMapper_Exception
     */
    public function get_epic(string $epicKey)
    {
        try {
            $es = new EpicService();

            $epic = $es->getEpic($epicKey);

            $this->assertNotNull($epicKey);
            //Dumper::dump($epic);
        } catch (JiraException $e) {
            $this->fail('get_epic Failed : '.$e->getMessage());
        }
    }

}
