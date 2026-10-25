<?php

namespace JiraRestApi\Test;

use JiraRestApi\Issue\JqlQuery;
use PHPUnit\Framework\TestCase;

class JqlQueryTest extends TestCase
{
    /** @test */
    public function set_custom_field_uses_equality_by_default(): void
    {
        $jql = (new JqlQuery())
            ->setProject('TEST')
            ->setCustomField('My Custom Field', 'value');

        $this->assertSame('"project" = "TEST" and "My Custom Field" = "value"', $jql->getQuery());
    }

    /**
     * @test
     *
     * @see https://github.com/lesstif/php-jira-rest-client/issues/569
     */
    public function set_custom_field_accepts_operator(): void
    {
        $jql = (new JqlQuery())
            ->setCustomField('cf[10000]', 'Maintenance', JqlQuery::KEYWORD_AND, JqlQuery::OPERATOR_CONTAINS)
            ->setCustomField('Account.name', 'Research', JqlQuery::KEYWORD_OR, JqlQuery::OPERATOR_NOT_EQUALS);

        $this->assertSame('"cf[10000]" ~ "Maintenance" or "Account.name" != "Research"', $jql->getQuery());
    }
}
