<?php

use JiraRestApi\HTTPException;

class CurlTest extends PHPUnit_Framework_TestCase
{
    public function testCurlPost()
    {
        $this->markTestIncomplete();
        try {
            $config = getHostConfig();

            $config['host'] = 'http://requestb.in/vqid8qvq';

            $j = new \JiraRestApi\JiraClient($config, getOptions());

            $post_data = ['name' => 'value'];

            $http_status = 0;
            $ret = $j->exec('/', json_encode($post_data), $http_status);

            var_dump($ret);
            $this->assertTrue(true);
        } catch (HTTPException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }
}
