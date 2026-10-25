<?php

namespace JiraRestApi\Test;

use JiraRestApi\Configuration\ArrayConfiguration;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\Transition;
use JiraRestApi\JiraException;
use JiraRestApi\Project\ProjectService;
use PHPUnit\Framework\TestCase;

/**
 * Transition by the untranslated status name works regardless of the user's language.
 *
 * @see https://github.com/lesstif/php-jira-rest-client/issues/323
 */
class TransitionUntranslatedNameTest extends TestCase
{
    // statuses of a project for a user whose language is Dutch (Jira Cloud).
    public const PROJECT_STATUSES = '[{"id":"10001","name":"Taak","subtask":false,"statuses":[
        {"id":"10306","name":"Te doen","untranslatedName":"To Do"},
        {"id":"10307","name":"Gereed","untranslatedName":"Done"}
    ]}]';

    public const ISSUE_TRANSITIONS = '{"transitions":[
        {"id":"11","name":"Te doen","to":{"id":"10306","name":"Te doen"}},
        {"id":"41","name":"Gereed","to":{"id":"10307","name":"Gereed"}}
    ]}';

    private function createService(): IssueService
    {
        $config = new ArrayConfiguration([
            'jiraHost'       => 'http://localhost',
            'jiraUser'       => 'user',
            'jiraPassword'   => 'pass',
            'jiraLogEnabled' => false,
        ]);

        return new class($config) extends IssueService {
            public array $requests = [];

            public function exec(string $context, array|string|null $post_data = null, ?string $custom_request = null, ?string $cookieFile = null): string|bool
            {
                $this->requests[] = ['context' => $context, 'data' => $post_data];

                if (str_ends_with($context, '/transitions') && $custom_request === 'POST') {
                    return true;
                }

                return TransitionUntranslatedNameTest::ISSUE_TRANSITIONS;
            }

            protected function createProjectService(): ProjectService
            {
                return new class($this->getConfiguration()) extends ProjectService {
                    public function exec(string $context, array|string|null $post_data = null, ?string $custom_request = null, ?string $cookieFile = null): string|bool
                    {
                        return TransitionUntranslatedNameTest::PROJECT_STATUSES;
                    }
                };
            }
        };
    }

    /** @test */
    public function find_transition_id_by_untranslated_status_name(): void
    {
        $service = $this->createService();

        // the transition id, not the status id(10307).
        $this->assertSame('41', $service->findTransitonIdByUntranslatedName('TEST-1', 'Done'));
        // the localized status name works too.
        $this->assertSame('41', $service->findTransitonIdByUntranslatedName('TEST-1', 'Gereed'));
    }

    /** @test */
    public function transition_with_untranslated_name_posts_the_transition_id(): void
    {
        $service = $this->createService();

        $transition = new Transition();
        $transition->setUntranslatedName('Done');

        $service->transition('TEST-1', $transition);

        $post = end($service->requests);
        $this->assertSame('/issue/TEST-1/transitions', $post['context']);
        $this->assertSame('41', json_decode($post['data'], true)['transition']['id']);
    }

    /** @test */
    public function unknown_untranslated_name_throws_exception(): void
    {
        $this->expectException(JiraException::class);

        $this->createService()->findTransitonIdByUntranslatedName('TEST-1', 'Closed');
    }
}
