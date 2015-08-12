<?php JiraRestApi\Support;

use Illuminate\Support\ServiceProvider;
use JiraRestApi\Project\ProjectService;

class LaravelJiraServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $config = [
            'jira_host' => env('JIRA_HOST'),
            'jira_user' => env('JIRA_USER'),
            'jira_password' => env('JIRA_PASS'),
            'curlopt_ssl_verifyhost' => env('CURLOPT_SSL_VERIFYHOST', false),
            'curlopt_ssl_verifypeer' => env('CURLOPT_SSL_VERIFYPEER', false),
            'curlopt_verbose' => env('CURLOPT_VERBOSE', false),
        ];

        $logger = new Logger('JiraClient');
        $logger->log->pushHandler(new StreamHandler('jira-rest-client.log', Logger::WARNING));

        $this->app->bind('JiraProjectService', function () {
            $service = new ProjectService($config);
            $service->setLogger($logger);
            return $service;
        });

        $this->app->bind('JiraIssueService', function () {
            $service = new IssueService($config);
            $service->setLogger($logger);
            return $service;
        });
    }

    private function convertLogLevel($log_level)
    {
        if ($log_level == 'DEBUG') {
            return Logger::DEBUG;
        } elseif ($log_level == 'INFO') {
            return Logger::DEBUG;
        } elseif ($log_level == 'WARNING') {
            return Logger::WARNING;
        } elseif ($log_level == 'ERROR') {
            return Logger::ERROR;
        } else {
            return Logger::WARNING;
        }
    }
}
