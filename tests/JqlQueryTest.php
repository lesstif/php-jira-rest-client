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

    /**
     * @test
     *
     * @see https://github.com/lesstif/php-jira-rest-client/issues/230
     */
    public function quote_escapes_double_quote_and_backslash(): void
    {
        $this->assertSame('"say \"hi\""', JqlQuery::quote('say "hi"'));
        $this->assertSame('"C:\\\\temp"', JqlQuery::quote('C:\temp'));
    }

    /**
     * @test
     *
     * @see https://github.com/lesstif/php-jira-rest-client/issues/230
     */
    public function quote_text_escapes_text_search_special_characters(): void
    {
        $this->assertSame(
            '"session_start\\\\(\\\\)\\\\: failed \\\\(path\\\\: \\\\/var\\\\/lib\\\\)"',
            JqlQuery::quoteText('session_start(): failed (path: /var/lib)')
        );
        $this->assertSame('"say \\\\\"hi\\\\\" a\\\\\\\\b"', JqlQuery::quoteText('say "hi" a\b'));
        $this->assertSame('"\\\\+\\\\-\\\\&\\\\|\\\\!\\\\{\\\\}\\\\[\\\\]\\\\^\\\\~\\\\*\\\\?"', JqlQuery::quoteText('+-&|!{}[]^~*?'));
    }

    /** @test */
    public function add_text_search_expression(): void
    {
        $jql = (new JqlQuery())
            ->setProject('TEST')
            ->addTextSearchExpression(JqlQuery::FIELD_SUMMARY, 'index.php(14)')
            ->addTextSearchExpression(JqlQuery::FIELD_DESCRIPTION, 'a+b', JqlQuery::KEYWORD_OR, JqlQuery::OPERATOR_DOES_NOT_CONTAIN);

        $this->assertSame(
            '"project" = "TEST" and "summary" ~ "index.php\\\\(14\\\\)" or "description" !~ "a\\\\+b"',
            $jql->getQuery()
        );
    }
}
