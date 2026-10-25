<?php

namespace JiraRestApi\Test;

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\Property;
use JiraRestApi\User\UserService;
use PHPUnit\Framework\TestCase;

/**
 * @see https://github.com/lesstif/php-jira-rest-client/issues/262
 */
class UserPropertyTest extends TestCase
{
    private function createService(array $responses): UserService
    {
        $config = new ArrayConfiguration([
            'jiraHost'       => 'http://localhost',
            'jiraUser'       => 'user',
            'jiraPassword'   => 'pass',
            'jiraLogEnabled' => false,
        ]);

        return new class($responses, $config) extends UserService {
            public array $requests = [];

            public function __construct(private array $responses, ArrayConfiguration $config)
            {
                parent::__construct($config);
            }

            public function exec(string $context, array|string|null $post_data = null, ?string $custom_request = null, ?string $cookieFile = null): string|bool
            {
                $this->requests[] = ['context' => $context, 'data' => $post_data, 'method' => $custom_request];

                return array_shift($this->responses);
            }
        };
    }

    /** @test */
    public function get_property_keys(): void
    {
        $service = $this->createService([
            '{"keys":[{"self":"http://localhost/rest/api/2/user/properties/a","key":"a"},{"self":"http://localhost/rest/api/2/user/properties/b","key":"b"}]}',
        ]);

        $this->assertSame(['a', 'b'], $service->getPropertyKeys(['username' => 'lesstif']));
        $this->assertSame('/user/properties?username=lesstif', $service->requests[0]['context']);
    }

    /** @test */
    public function get_property_with_json_value(): void
    {
        $service = $this->createService(['{"key":"my.settings","value":{"theme":"dark","size":3}}']);

        $property = $service->getProperty(['accountId' => '5b10a2844c20165700ede21g'], 'my.settings');

        $this->assertSame('my.settings', $property->key);
        $this->assertSame('dark', $property->value->theme);
        $this->assertSame(3, $property->value->size);
        $this->assertSame('/user/properties/my.settings?accountId=5b10a2844c20165700ede21g', $service->requests[0]['context']);
    }

    /** @test */
    public function set_property(): void
    {
        $service = $this->createService([true]);

        $property = new Property();
        $property->key = 'my.settings';
        $property->value = ['theme' => 'dark'];

        $service->setProperty(['username' => 'lesstif'], $property);

        $this->assertSame([
            'context' => '/user/properties/my.settings?username=lesstif',
            'data'    => '{"theme":"dark"}',
            'method'  => 'PUT',
        ], $service->requests[0]);
    }

    /** @test */
    public function delete_property(): void
    {
        $service = $this->createService([true]);

        $service->deleteProperty(['userKey' => 'JIRAUSER10100'], 'my.settings');

        $this->assertSame([
            'context' => '/user/properties/my.settings?userKey=JIRAUSER10100',
            'data'    => null,
            'method'  => 'DELETE',
        ], $service->requests[0]);
    }
}
