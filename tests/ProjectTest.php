<?php

use JiraRestApi\Project\ProjectService;

class ProjectTest extends PHPUnit_Framework_TestCase 
{
    public function testGetProject()
    {
    	//$this->markTestIncomplete();
		try {
			$proj = new ProjectService();

			$p = $proj->get('TEST');
			
			print_r($p->lead);
			foreach ($p->components as $c) {
				echo ("COM : " . $c->name . "\n");
			}
		} catch (HTTPException $e) {
			$this->assertTrue(FALSE, $e->getMessage());
		}
	}

	public function testGetProjectLists()
    {
    	//$this->markTestIncomplete();
		try {
			$proj = new ProjectService();

			$prjs = $proj->getAllProjects();

			$i = 0;
			foreach ($prjs as $p) {
				echo sprintf("Project Key:%s, Id:%s, Name:%s, projectCategory: %s\n",
					$p->key, $p->id, $p->name, $p->projectCategory['name']
					);
					
			}			
		} catch (HTTPException $e) {
			$this->assertTrue(FALSE, $e->getMessage());
		}
	}
	//
}

?>
