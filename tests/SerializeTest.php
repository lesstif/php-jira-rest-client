<?php

namespace JiraRestApi\Test;

use PHPUnit\Framework\TestCase;
use JiraRestApi\Dumper;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\Reporter;

class SerializeTest extends TestCase
{
    /**
     * @see https://github.com/lesstif/php-jira-rest-client/issues/18
     */
    public function testDefaultAssignee()
    {
        $r = new Reporter();

        $r->name = '-1';
        $r->emailAddress = 'user@example.com';
        // empty string values must be dropped from the serialized array.
        $r->displayName = '';
        // typed properties that were never assigned (such as array $avatarUrls) must not appear either.

        $d = $r->jsonSerialize();

        $this->assertEquals('-1', $d['name']);
        $this->assertEquals(false, array_key_exists('displayName', $d));
        $this->assertEquals(false, array_key_exists('avatarUrls', $d));
    }

    public function testSerialize()
    {
        $r = new Reporter();

        $r->name = 'KwangSeob Jeong';
        $r->emailAddress = 'user@example.com';
        $r->avatarUrls = ['48x48' => 'http://my.avatar.com/avatarUrls'];
        $r->displayName = 'lesstif';

        $d = $r->toArray(['name', 'emailAddress'], $excludeMode = true);
        Dumper::dump($d);

        // serialized array have not 'name' and 'emailAddress' keys.
        $this->assertEquals(false, array_key_exists('name', $d), '"name" key is exists!.');
        $this->assertEquals(false, array_key_exists('emailAddress', $d));

        $d = $r->toArray(['name', 'emailAddress'], $excludeMode = false);

        // serialized array must have only 'name' and 'emailAddress' keys.
        $this->assertEquals(true, array_key_exists('name', $d), '"name" key is not exists!.');
        $this->assertEquals(true, array_key_exists('emailAddress', $d));
        $this->assertEquals(2, count($d));
        Dumper::dump($d);
    }

    /**
     * @see https://github.com/lesstif/php-jira-rest-client/issues/558
     */
    public function testFixVersionsDefaultsToNull()
    {
        $field = new IssueField();

        $this->assertNull($field->fixVersions);
        $this->assertArrayNotHasKey('fixVersions', $field->jsonSerialize());

        // Jira may return null for fixVersions; mapping must not fail.
        $mapped = (new \JsonMapper())->map(json_decode('{"fixVersions": null}'), new IssueField(true));
        $this->assertNull($mapped->fixVersions);
        $this->assertSame([], $mapped->getFixVersions());

        $mapped = (new \JsonMapper())->map(json_decode('{"fixVersions": [{"name": "1.0.1"}]}'), new IssueField(true));
        $this->assertCount(1, $mapped->getFixVersions());
    }
}
