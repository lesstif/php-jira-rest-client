<?php

namespace JiraRestApi\Issue;

/**
 * Class JqlQuery used to build JQL queries for issue searching.
 *
 * Usage example:
 * ```
 * $query = new JqlQuery();
 * $query->setProject('Project-Key')
 *     ->setAssignee('someUser');
 *
 * $issues = $issueService->search($query->getQuery());
 * ```
 *
 * @see https://docs.atlassian.com/software/jira/docs/api/REST/latest/#api/2/search search method
 * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-861257209.html jql usage
 * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html jql
 *      fields reference
 * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-operators-reference-861257221.html jql
 *      operators reference
 * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-keywords-reference-861257220.html jql
 *      keywords reference
 */
class JqlQuery
{
    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-operators-reference-861257221.html#Advancedsearching-operatorsreference-EQUALSEQUALS:=
     *      jql reference
     */
    const OPERATOR_EQUALS = '=';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-operators-reference-861257221.html#Advancedsearching-operatorsreference-NOT_EQUALSNOTEQUALS:!=
     *      jql reference
     */
    const OPERATOR_NOT_EQUALS = '!=';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-operators-reference-861257221.html#Advancedsearching-operatorsreference-GREATER_THAN%3EGREATERTHAN:%3E
     *      jql reference
     */
    const OPERATOR_GREATER_THAN = '>';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-operators-reference-861257221.html#Advancedsearching-operatorsreference-GREATER_THAN_EQUALS%3E=GREATERTHANEQUALS:%3E=
     *      jql reference
     */
    const OPERATOR_GREATER_THAN_EQUALS = '>=';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-operators-reference-861257221.html#Advancedsearching-operatorsreference-LESS_THAN%3CLESSTHAN:%3C
     *      jql reference
     */
    const OPERATOR_LESS_THAN = '<';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-operators-reference-861257221.html#Advancedsearching-operatorsreference-LESS_THAN_EQUALS%3C=LESSTHANEQUALS:%3C=
     *      jql reference
     */
    const OPERATOR_LESS_THAN_EQUALS = '<=';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-operators-reference-861257221.html#Advancedsearching-operatorsreference-ININ
     *      jql reference
     */
    const OPERATOR_IN = 'in';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-operators-reference-861257221.html#Advancedsearching-operatorsreference-NOT_INNOTIN
     *      jql reference
     */
    const OPERATOR_NOT_IN = 'not in';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-operators-reference-861257221.html#Advancedsearching-operatorsreference-CONTAINS~CONTAINS:~
     *      jql reference
     */
    const OPERATOR_CONTAINS = '~';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-operators-reference-861257221.html#Advancedsearching-operatorsreference-DOES_NOT_CONTAINDOESNOTCONTAIN:!~
     *      jql reference
     */
    const OPERATOR_DOES_NOT_CONTAIN = '!~';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-operators-reference-861257221.html#Advancedsearching-operatorsreference-ISIS
     *      jql reference
     */
    const OPERATOR_IS = 'is';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-operators-reference-861257221.html#Advancedsearching-operatorsreference-IS_NOTISNOT
     *      jql reference
     */
    const OPERATOR_IS_NOT = 'is not';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-operators-reference-861257221.html#Advancedsearching-operatorsreference-WASWAS
     *      jql reference
     */
    const OPERATOR_WAS = 'was';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-operators-reference-861257221.html#Advancedsearching-operatorsreference-WAS_INWASIN
     *      jql reference
     */
    const OPERATOR_WAS_IN = 'was in';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-operators-reference-861257221.html#Advancedsearching-operatorsreference-WAS_NOT_INWASNOTIN
     *      jql reference
     */
    const OPERATOR_WAS_NOT_IN = 'was not in';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-operators-reference-861257221.html#Advancedsearching-operatorsreference-WAS_NOTWASNOT
     *      jql reference
     */
    const OPERATOR_WAS_NOT = 'was not';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-operators-reference-861257221.html#Advancedsearching-operatorsreference-CHANGEDCHANGED
     *      jql reference
     */
    const OPERATOR_CHANGED = 'changed';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-keywords-reference-861257220.html#Advancedsearching-keywordsreference-ANDAND
     *      jql reference
     */
    const KEYWORD_AND = 'and';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-keywords-reference-861257220.html#Advancedsearching-keywordsreference-OROR
     *      jql reference
     */
    const KEYWORD_OR = 'or';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-keywords-reference-861257220.html#Advancedsearching-keywordsreference-NOTNOT
     *      jql reference
     */
    const KEYWORD_NOT = 'not';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-keywords-reference-861257220.html#Advancedsearching-keywordsreference-EMPTYEMPTY
     *      jql reference
     */
    const KEYWORD_EMPTY = 'empty';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-keywords-reference-861257220.html#Advancedsearching-keywordsreference-NULLNULL
     *      jql reference
     */
    const KEYWORD_NULL = 'null';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-keywords-reference-861257220.html#Advancedsearching-keywordsreference-ORDER_BYORDERBY
     *      jql reference
     */
    const KEYWORD_ORDER_BY = 'order by';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-affectedVersionAffectedVersionAffectedversion
     *      jql field reference
     */
    const FIELD_AFFECTED_VERSION = 'affectedVersion';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-ApprovalsApprovals
     *      jql field reference
     */
    const FIELD_APPROVALS = 'approvals';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-AssigneeAssignee
     *      jql field reference
     */
    const FIELD_ASSIGNEE = 'assignee';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-attachmentsAttachments
     *      jql field reference
     */
    const FIELD_ATTACHMENTS = 'attachments';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-CategoryCategory
     *      jql field reference
     */
    const FIELD_CATEGORY = 'category';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-CommentCommentsComment
     *      jql field reference
     */
    const FIELD_COMMENT = 'comment';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-ComponentComponent
     *      jql field reference
     */
    const FIELD_COMPONENT = 'component';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-CreatedCreatedDatecreatedDateCreated
     *      jql field reference
     */
    const FIELD_CREATED = 'created';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-CreatorCreator
     *      jql field reference
     */
    const FIELD_CREATOR = 'creator';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-CustomerRequestTypeCustomerRequestType
     *      jql field reference
     */
    const FIELD_CUSTOMER_REQUEST_TYPE = '"Customer Request Type"';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-DescriptionDescription
     *      jql field reference
     */
    const FIELD_DESCRIPTION = 'description';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-DueDueDatedueDateDue
     *      jql field reference
     */
    const FIELD_DUE = 'due';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-EnvironmentEnvironment
     *      jql field reference
     */
    const FIELD_ENVIRONMENT = 'environment';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-epic_linkEpiclink
     *      jql field reference
     */
    const FIELD_EPIC_LINK = '"epic link"';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-filterFilter
     *      jql field reference
     */
    const FIELD_FILTER = 'filter';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-FixVersionfixVersionFixversion
     *      jql field reference
     */
    const FIELD_FIX_VERSION = 'fixVersion';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-IssueIssuekey
     *      jql field reference
     */
    const FIELD_ISSUE_KEY = 'issueKey';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-labelsLabels
     *      jql field reference
     */
    const FIELD_LABELS = 'labels';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-LastViewedLastViewedDatelastViewedDateLastviewed
     *      jql field reference
     */
    const FIELD_LAST_VIEWED = 'lastViewed';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-LevelLevel
     *      jql field reference
     */
    const FIELD_LEVEL = 'level';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-originalEstimateOriginalEstimateOriginalestimate
     *      jql field reference
     */
    const FIELD_ORIGINAL_ESTIMATE = 'originalEstimate';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-ParentParent
     *      jql field reference
     */
    const FIELD_PARENT = 'parent';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-PriorityPriority
     *      jql field reference
     */
    const FIELD_PRIORITY = 'priority';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-ProjectProject
     *      jql field reference
     */
    const FIELD_PROJECT = 'project';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-remainingEstimateRemainingEstimateRemainingestimate
     *      jql field reference
     */
    const FIELD_REMAINING_ESTIMATE = 'remainingEstimate';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-ReporterReporter
     *      jql field reference
     */
    const FIELD_REPORTER = 'reporter';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-requestChannelTypeRequestchanneltype
     *      jql field reference
     */
    const FIELD_REQUEST_CHANNEL_TYPE = 'request-channel-type';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-requestLastActivityTimeRequestlastactivitytime
     *      jql field reference
     */
    const FIELD_REQUEST_LAST_ACTIVITY_TIME = 'request-last-activity-time';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-ResolutionResolution
     *      jql field reference
     */
    const FIELD_RESOLUTION = 'resolution';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-ResolvedResolutionDateresolutionDateResolved
     *      jql field reference
     */
    const FIELD_RESOLVED = 'resolved';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-SprintSprint
     *      jql field reference
     */
    const FIELD_SPRINT = 'sprint';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-StatusStatus
     *      jql field reference
     */
    const FIELD_STATUS = 'status';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-SummarySummary
     *      jql field reference
     */
    const FIELD_SUMMARY = 'summary';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-textTextText
     *      jql field reference
     */
    const FIELD_TEXT = 'text';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-TimeSpenttimeSpentTimespent
     *      jql field reference
     */
    const FIELD_TIME_SPENT = 'timeSpent';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-TypeType
     *      jql field reference
     */
    const FIELD_TYPE = 'type';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-UpdatedUpdatedDateupdatedDateUpdated
     *      jql field reference
     */
    const FIELD_UPDATED = 'updated';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-voterVoterVoter
     *      jql field reference
     */
    const FIELD_VOTER = 'voter';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-VotesVotes
     *      jql field reference
     */
    const FIELD_VOTES = 'votes';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-watcherWatcherWatcher
     *      jql field reference
     */
    const FIELD_WATCHER = 'watcher';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-WatchersWatchers
     *      jql field reference
     */
    const FIELD_WATCHERS = 'watchers';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-WorkLogAuthorworklogAuthorWorklogauthor
     *      jql field reference
     */
    const FIELD_WORKLOG_AUTHOR = 'worklogAuthor';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-WorkLogCommentworklogCommentWorklogcomment
     *      jql field reference
     */
    const FIELD_WORKLOG_COMMENT = 'worklogComment';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-WorkLogDateworklogDateWorklogdate
     *      jql field reference
     */
    const FIELD_WORKLOG_DATE = 'worklogDate';

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-fields-reference-861257219.html#Advancedsearching-fieldsreference-WorkRatioworkRatioWorkratio
     *      jql field reference
     */
    const FIELD_WORK_RATIO = 'workRatio';

    protected $query = '';

    /**
     * Quote value. Also can be used with JqlFunctions.
     *
     * Example: JqlQuery::quote('some "value"') returns '"some \\"value\\""'
     *
     * JqlQuery::quote(JqlFunction::now()) returns 'now()'
     *
     * @param string $value
     *
     * @return string
     */
    public static function quote($value)
    {
        if ($value instanceof JqlFunction) {
            return $value->expression;
        }

        $value = str_replace('"', '\\\\"', $value);

        return '"'.$value.'"';
    }

    /**
     * Quote jql field name.
     * Doesn't quote already "quoted" or 'quoted' strings.
     *
     * Example: JqlQuery::quoteField(JqlQuery::FIELD_PROJECT) returns '"project"'
     *
     * JqlQuery::quoteField('"Quoted Custom Field"') returns '"Quoted Custom Field"' (no changes)
     *
     * @param string $name
     *
     * @return string
     */
    public static function quoteField($name)
    {
        $first = substr($name, 0, 1);
        $last = substr($name, -1, 1);

        if ($first === '"' && $last === '"') {
            return $name; // already "quoted"
        }
        if ($first === "'" && $last === "'") {
            return $name; // already 'quoted'
        }

        return self::quote($name);
    }

    protected function joinExpression($expression, $joinOperation)
    {
        if (!empty($this->query)) {
            $this->query .= " $joinOperation ";
        }
        $this->query .= $expression;
    }

    /**
     * Get built JQL query string.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Adds any text expression to query.
     *
     * Example: $query->addAnyExpression('and project = "projectKey"');
     *
     * @param string $expression
     *
     * @return static $this
     */
    public function addAnyExpression($expression)
    {
        $this->query .= " $expression";

        return $this;
    }

    /**
     * Adds expression with any operator and any field to query.
     *
     * Example: $query->addExpression({@see JqlQuery::FIELD_PROJECT}, {@see JqlQuery::OPERATOR_EQUALS}, 'projectKey')
     *
     * Other Example: $query->addExpression('project', '=', 'projectKey');
     *
     * Do not use this method with array values.
     * Use addInExpression, addNotInExpression or addAnyExpression instead.
     *
     * @param string $field            field name
     * @param string $operator         supported scalar operator (see OPERATOR_ constants.
     *                                 {@see JqlQuery::OPERATOR_EQUALS} etc.)
     * @param string $value            field value
     * @param string $logicLinkKeyword use {@see JqlQuery::KEYWORD_AND} or {@see JqlQuery::KEYWORD_OR}
     *                                 to set join logical operation. Default {@see JqlQuery::KEYWORD_AND}.
     *
     * @return JqlQuery
     */
    public function addExpression($field, $operator, $value, $logicLinkKeyword = self::KEYWORD_AND)
    {
        $this->joinExpression(self::quoteField($field)." $operator ".self::quote($value),
            $logicLinkKeyword);

        return $this;
    }

    /**
     * Adds 'in' expression with any field to query.
     *
     * Example: $query->addInExpression({@see JqlQuery::FIELD_ASSIGNEE}, ['user1', 'user2'])
     *
     * @param string   $field            field name
     * @param string[] $values           field values array
     * @param string   $logicLinkKeyword use {@see JqlQuery::KEYWORD_AND} or {@see JqlQuery::KEYWORD_OR}
     *                                   to set join logical operation. Default {@see JqlQuery::KEYWORD_AND}.
     *
     * @return JqlQuery
     */
    public function addInExpression($field, $values, $logicLinkKeyword = self::KEYWORD_AND)
    {
        $valuesQuoted = [];
        foreach ($values as $value) {
            $valuesQuoted[] = self::quote($value);
        }
        $this->joinExpression(self::quoteField($field).' in ('.implode(', ', $valuesQuoted).')',
            $logicLinkKeyword);

        return $this;
    }

    /**
     * Adds 'not in' expression with any field to query.
     *
     * Example: $query->addNotInExpression({@see JqlQuery::FIELD_ASSIGNEE}, ['user1', 'user2'])
     *
     * @param string   $field            field name
     * @param string[] $values           field values array
     * @param string   $logicLinkKeyword use {@see JqlQuery::KEYWORD_AND} or {@see JqlQuery::KEYWORD_OR}
     *                                   to set join logical operation. Default {@see JqlQuery::KEYWORD_AND}.
     *
     * @return JqlQuery
     */
    public function addNotInExpression($field, $values, $logicLinkKeyword = self::KEYWORD_AND)
    {
        $valuesQuoted = [];
        foreach ($values as $value) {
            $valuesQuoted[] = self::quote($value);
        }
        $this->joinExpression(self::quoteField($field).' not in ('.implode(', ', $valuesQuoted).')',
            $logicLinkKeyword);

        return $this;
    }

    /**
     * Adds 'is null' expression with any field to query.
     *
     * Example: $query->addIsNullExpression({@see JqlQuery::FIELD_ASSIGNEE})
     *
     * @param string $field            field name
     * @param string $logicLinkKeyword use {@see JqlQuery::KEYWORD_AND} or {@see JqlQuery::KEYWORD_OR}
     *                                 to set join logical operation. Default {@see JqlQuery::KEYWORD_AND}.
     *
     * @return JqlQuery
     */
    public function addIsNullExpression($field, $logicLinkKeyword = self::KEYWORD_AND)
    {
        $this->joinExpression(self::quoteField($field).' is null', $logicLinkKeyword);

        return $this;
    }

    /**
     * Adds 'is not null' expression with any field to query.
     *
     * Example: $query->addIsNotNullExpression({@see JqlQuery::FIELD_ASSIGNEE})
     *
     * @param string $field            field name
     * @param string $logicLinkKeyword use {@see JqlQuery::KEYWORD_AND} or {@see JqlQuery::KEYWORD_OR}
     *                                 to set join logical operation. Default {@see JqlQuery::KEYWORD_AND}.
     *
     * @return JqlQuery
     */
    public function addIsNotNullExpression($field, $logicLinkKeyword = self::KEYWORD_AND)
    {
        $this->joinExpression(self::quoteField($field).' is not null', $logicLinkKeyword);

        return $this;
    }

    /**
     * Adds 'is empty' expression with any field to query.
     *
     * Example: $query->addIsEmptyExpression({@see JqlQuery::FIELD_ASSIGNEE})
     *
     * @param string $field            field name
     * @param string $logicLinkKeyword use {@see JqlQuery::KEYWORD_AND} or {@see JqlQuery::KEYWORD_OR}
     *                                 to set join logical operation. Default {@see JqlQuery::KEYWORD_AND}.
     *
     * @return JqlQuery
     */
    public function addIsEmptyExpression($field, $logicLinkKeyword = self::KEYWORD_AND)
    {
        $this->joinExpression(self::quoteField($field).' is empty', $logicLinkKeyword);

        return $this;
    }

    /**
     * Adds 'is not empty' expression with any field to query.
     *
     * Example: $query->addIsNotEmptyExpression({@see JqlQuery::FIELD_ASSIGNEE})
     *
     * @param string $field            field name
     * @param string $logicLinkKeyword use {@see JqlQuery::KEYWORD_AND} or {@see JqlQuery::KEYWORD_OR}
     *                                 to set join logical operation. Default {@see JqlQuery::KEYWORD_AND}.
     *
     * @return JqlQuery
     */
    public function addIsNotEmptyExpression($field, $logicLinkKeyword = self::KEYWORD_AND)
    {
        $this->joinExpression(self::quoteField($field).' is not empty', $logicLinkKeyword);

        return $this;
    }

    /**
     * Add custom field test to equality.
     * This method can be used with system fields and 'custom fields' (defined by user).
     *
     * Example: $query->setCustomField('My Custom field', 'value')
     * appends 'and "My Custom field" = "value"' expression
     *
     * @param string $field            custom field name
     * @param string $value            value to equality check
     * @param string $logicLinkKeyword use {@see JqlQuery::KEYWORD_AND} or {@see JqlQuery::KEYWORD_OR}
     *                                 to set join logical operation. Default {@see JqlQuery::KEYWORD_AND}.
     *
     * @return JqlQuery
     */
    public function setCustomField($field, $value, $logicLinkKeyword = self::KEYWORD_AND)
    {
        return $this->addExpression($field, self::OPERATOR_EQUALS, $value, $logicLinkKeyword);
    }

    /**
     * Adds project condition.
     *
     * Example: $query->setProject('projectKey')
     *
     * @param string $idOrKey          project id or key
     * @param string $logicLinkKeyword use {@see JqlQuery::KEYWORD_AND} or {@see JqlQuery::KEYWORD_OR}
     *                                 to set join logical operation. Default {@see JqlQuery::KEYWORD_AND}.
     *
     * @return JqlQuery
     */
    public function setProject($idOrKey, $logicLinkKeyword = self::KEYWORD_AND)
    {
        return $this->addExpression(self::FIELD_PROJECT, self::OPERATOR_EQUALS, $idOrKey, $logicLinkKeyword);
    }

    /**
     * Adds priority condition.
     *
     * Example: $query->setPriority('high')
     *
     * @param string $priority         priority id or name
     * @param string $logicLinkKeyword use {@see JqlQuery::KEYWORD_AND} or {@see JqlQuery::KEYWORD_OR}
     *                                 to set join logical operation. Default {@see JqlQuery::KEYWORD_AND}.
     *
     * @return JqlQuery
     */
    public function setPriority($priority, $logicLinkKeyword = self::KEYWORD_AND)
    {
        return $this->addExpression(self::FIELD_PRIORITY, self::OPERATOR_EQUALS, $priority, $logicLinkKeyword);
    }

    /**
     * Adds assignee condition.
     *
     * Example: $query->setAssignee('user1')
     *
     * @param string $user             user id or name
     * @param string $logicLinkKeyword use {@see JqlQuery::KEYWORD_AND} or {@see JqlQuery::KEYWORD_OR}
     *                                 to set join logical operation. Default {@see JqlQuery::KEYWORD_AND}.
     *
     * @return JqlQuery
     */
    public function setAssignee($user, $logicLinkKeyword = self::KEYWORD_AND)
    {
        return $this->addExpression(self::FIELD_ASSIGNEE, self::OPERATOR_EQUALS, $user, $logicLinkKeyword);
    }

    /**
     * Adds issue status condition.
     *
     * Example: $query->setStatus('Open')
     *
     * @param string $status           issue status id or name
     * @param string $logicLinkKeyword use {@see JqlQuery::KEYWORD_AND} or {@see JqlQuery::KEYWORD_OR}
     *                                 to set join logical operation. Default {@see JqlQuery::KEYWORD_AND}.
     *
     * @return JqlQuery
     */
    public function setStatus($status, $logicLinkKeyword = self::KEYWORD_AND)
    {
        return $this->addExpression(self::FIELD_STATUS, self::OPERATOR_EQUALS, $status, $logicLinkKeyword);
    }

    /**
     * Adds issue type condition.
     *
     * Example: $query->setType('bug')
     *
     * @param string $type             issue type id or name
     * @param string $logicLinkKeyword use {@see JqlQuery::KEYWORD_AND} or {@see JqlQuery::KEYWORD_OR}
     *                                 to set join logical operation. Default {@see JqlQuery::KEYWORD_AND}.
     *
     * @return JqlQuery
     */
    public function setType($type, $logicLinkKeyword = self::KEYWORD_AND)
    {
        return $this->addExpression(self::FIELD_TYPE, self::OPERATOR_EQUALS, $type, $logicLinkKeyword);
    }
}
