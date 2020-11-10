<?php

use JiraRestApi\Dumper;
use JiraRestApi\Issue\Comment;
use JiraRestApi\Issue\Issue;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Transition;
use JiraRestApi\Exceptions\JiraException;
use JiraRestApi\Exceptions\HTTPException;

class IssueSecuritySechemTest extends \PHPUnit\Framework\TestCase
{
    public function testGetAllSecuritySchemes()
    {
        try {
            $issueService = new IssueService();

            $securitySchemes = $issueService->getAllIssueSecuritySchemes();

            $this->assertGreaterThan(1, count($securitySchemes), 'security scheme must greater than 1');
            $this->assertEquals(true, array_key_exists('id', $securitySchemes[0]), 'security id not found');

            return $securitySchemes;
        } catch (HTTPException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    /**
     * @depends testGetAllSecuritySchemes
     *
     * @param $securitySchem
     * @throws Exception
     * @throws JiraException
     * @throws JsonMapper_Exception
     */
    public function testGetSecurityScheme($securitySchemes)
    {
        $securityId = 0;

        try {
            $issueService = new IssueService();

            foreach ($securitySchemes as $s) {
                $ss = $issueService->getIssueSecuritySchemes($s->id);

                $this->assertObjectHasAttribute('id', $ss, 'security id not found');
                $this->assertObjectHasAttribute('levels', $ss, 'security level not found');

                if ($securityId === 0)
                    $securityId = $ss->id;
            }

            return $securityId;
        } catch (HTTPException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    /**
     * @depends testGetSecurityScheme
     *
     * @param $schemeId
     * @return mixed
     * @throws Exception
     * @throws JsonMapper_Exception
     */
    public function testCreateIssueWithSecurityScheme($securityId)
    {
        try {
            $issueField = new IssueField();

            $issueField->set_ProjectKey('TEST')
                        ->set_Summary("issue security level test")
                        ->set_AssigneeName('lesstif')
                        ->set_PriorityName('Critical')
                        ->set_IssueType('Bug')
                        ->set_Description('Full description for issue')
                        ->add_Version(['1.0.1', '1.0.3'])
                        ->add_Components(['Component-1', 'Component-2'])
                        ->setSecurity($securityId)
            ;

            $issueService = new IssueService();

            $ret = $issueService->create($issueField);

            $this->assertInstanceOf(Issue::class, $ret);

        } catch (JiraException $e) {
            $this->assertTrue(false, 'Create Failed : '.$e->getMessage());
        }
    }
}
