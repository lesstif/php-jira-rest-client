<?php

namespace JiraRestApi\Issue;

/**
 * Class JqlFunction used with JqlQuery to set JQL function calls as values in expressions.
 *
 * Example:
 * ```
 * $jql = new JqlQuery();
 * $jql->setAssignee(JqlFunction::currentUser())
 *     ->addExpression('issue', 'in', JqlFunction::IssueHistory());
 * ```
 */
class JqlFunction
{
    public $expression = '';

    public function __construct($expression)
    {
        $this->expression = $expression;
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-approvedapproved()
     *      jql function reference
     *
     * @return JqlFunction
     */
    public static function approved()
    {
        return new self('approved()');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-approverapprover()
     *      jql function reference
     *
     * @param string $user
     *
     * @return JqlFunction
     */
    public static function approver($user)
    {
        return new self('approver('.implode(', ', func_get_args()).')');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-cascadeOptioncascadeOption()
     *      jql function reference
     *
     * @param string      $parentOption
     * @param string|null $childOption
     *
     * @return JqlFunction
     */
    public static function cascadeOption($parentOption, $childOption = null)
    {
        $expression = "cascadeOption($parentOption";
        if ($childOption !== null) {
            $expression .= ",$childOption";
        }
        $expression .= ')';

        return new self($expression);
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-closedSprintsclosedSprints()
     *      jql function reference
     *
     * @return JqlFunction
     */
    public static function closedSprints()
    {
        return new self('closedSprints()');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-componentsLeadByUsercomponentsLeadByUser()
     *      jql function reference
     *
     * @param null|string $user
     *
     * @return JqlFunction
     */
    public static function componentsLeadByUser($user = null)
    {
        return new self("componentsLeadByUser($user)");
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-currentLogincurrentLogin()
     *      jql function reference
     *
     * @return JqlFunction
     */
    public static function currentLogin()
    {
        return new self('currentLogin()');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-currentUsercurrentUser()
     *      jql function reference
     *
     * @return JqlFunction
     */
    public static function currentUser()
    {
        return new self('currentUser()');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-earliestUnreleasedVersionearliestUnreleasedVersion()
     *      jql function reference
     *
     * @param string $project
     *
     * @return JqlFunction
     */
    public static function earliestUnreleasedVersion($project)
    {
        return new self("earliestUnreleasedVersion($project)");
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-endOfDayendOfDay()
     *      jql function reference
     *
     * @param string|null $inc
     *
     * @return JqlFunction
     */
    public static function endOfDay($inc = null)
    {
        if ($inc !== null) {
            $inc = JqlQuery::quote($inc);
        }

        return new self("endOfDay($inc)");
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-endOfMonthendOfMonth()
     *      jql function reference
     *
     * @param string|null $inc
     *
     * @return JqlFunction
     */
    public static function endOfMonth($inc = null)
    {
        if ($inc !== null) {
            $inc = JqlQuery::quote($inc);
        }

        return new self("endOfMonth($inc)");
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-endOfWeekendOfWeek()
     *      jql function reference
     *
     * @param string|null $inc
     *
     * @return JqlFunction
     */
    public static function endOfWeek($inc = null)
    {
        if ($inc !== null) {
            $inc = JqlQuery::quote($inc);
        }

        return new self("endOfWeek($inc)");
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-endOfYearendOfYear()
     *      jql function reference
     *
     * @param string|null $inc
     *
     * @return JqlFunction
     */
    public static function endOfYear($inc = null)
    {
        if ($inc !== null) {
            $inc = JqlQuery::quote($inc);
        }

        return new self("endOfYear($inc)");
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-issueHistoryissueHistory()
     *      jql function reference
     *
     * @return JqlFunction
     */
    public static function issueHistory()
    {
        return new self('issueHistory()');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-issueHistoryissuesWithRemoteLinksByGlobalId()
     *      jql function reference
     *
     * @return JqlFunction
     */
    public static function issuesWithRemoteLinksByGlobalId()
    {
        return new self('issuesWithRemoteLinksByGlobalId()');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-lastLoginlastLogin()
     *      jql function reference
     *
     * @return JqlFunction
     */
    public static function lastLogin()
    {
        return new self('lastLogin()');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-latestReleasedVersionlatestReleasedVersion()
     *      jql function reference
     *
     * @param $project
     *
     * @return JqlFunction
     */
    public static function latestReleasedVersion($project)
    {
        return new self("latestReleasedVersion($project)");
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-linkedIssueslinkedIssues()
     *      jql function reference
     *
     * @param string      $issueKey
     * @param null|string $linkType
     *
     * @return JqlFunction
     */
    public static function linkedIssues($issueKey, $linkType = null)
    {
        $expression = "cascadeOption($issueKey";
        if ($linkType !== null) {
            $expression .= ', '.JqlQuery::quote($linkType);
        }
        $expression .= ')';

        return new self($expression);
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-membersOfmembersOf()
     *      jql function reference
     *
     * @param string $group
     *
     * @return JqlFunction
     */
    public static function membersOf($group)
    {
        return new self('membersOf('.JqlQuery::quote($group).')');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-myApprovalmyApproval()
     *      jql function reference
     *
     * @return JqlFunction
     */
    public static function myApproval()
    {
        return new self('myApproval()');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-approvedmyPending()
     *      jql function reference
     *
     * @return JqlFunction
     */
    public static function myPending()
    {
        return new self('myPending()');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-nownow()
     *      jql function reference
     *
     * @return JqlFunction
     */
    public static function now()
    {
        return new self('now()');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-openSprintsopenSprints()
     *      jql function reference
     *
     * @return JqlFunction
     */
    public static function openSprints()
    {
        return new self('openSprints()');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-approvedpending()
     *      jql function reference
     *
     * @return JqlFunction
     */
    public static function pending()
    {
        return new self('pending()');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-approvedpendingBy()
     *      jql function reference
     *
     * @param string $user
     *
     * @return JqlFunction
     */
    public static function pendingBy($user)
    {
        return new self('pendingBy('.implode(', ', func_get_args()).')');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-projectsLeadByUserprojectsLeadByUser()
     *      jql function reference
     *
     * @param string|null $username
     *
     * @return JqlFunction
     */
    public static function projectsLeadByUser($username = null)
    {
        return new self("projectsLeadByUser($username)");
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-projectsWhereUserHasPermissionprojectsWhereUserHasPermission()
     *      jql function reference
     *
     * @param string $permission
     *
     * @return JqlFunction
     */
    public static function projectsWhereUserHasPermission($permission)
    {
        return new self('projectsWhereUserHasPermission('.JqlQuery::quote($permission).')');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-projectsWhereUserHasRoleprojectsWhereUserHasRole()
     *      jql function reference
     *
     * @param string $roleName
     *
     * @return JqlFunction
     */
    public static function projectsWhereUserHasRole($roleName)
    {
        return new self('projectsWhereUserHasRole('.JqlQuery::quote($roleName).')');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-releasedVersionsreleasedVersions()
     *      jql function reference
     *
     * @param string|null $project
     *
     * @return JqlFunction
     */
    public static function releasedVersions($project = null)
    {
        return new self("releasedVersions($project)");
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-standardIssueTypesstandardIssueTypes()
     *      jql function reference
     *
     * @return JqlFunction
     */
    public static function standardIssueTypes()
    {
        return new self('standardIssueTypes()');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-startOfDaystartOfDay()
     *      jql function reference
     *
     * @param string|null $inc
     *
     * @return JqlFunction
     */
    public static function startOfDay($inc = null)
    {
        if ($inc !== null) {
            $inc = JqlQuery::quote($inc);
        }

        return new self("startOfDay($inc)");
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-startOfMonthstartOfMonth()
     *      jql function reference
     *
     * @param string|null $inc
     *
     * @return JqlFunction
     */
    public static function startOfMonth($inc = null)
    {
        if ($inc !== null) {
            $inc = JqlQuery::quote($inc);
        }

        return new self("startOfMonth($inc)");
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-startOfWeekstartOfWeek()
     *      jql function reference
     *
     * @param string|null $inc
     *
     * @return JqlFunction
     */
    public static function startOfWeek($inc = null)
    {
        if ($inc !== null) {
            $inc = JqlQuery::quote($inc);
        }

        return new self("startOfWeek($inc)");
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-startOfYearstartOfYear()
     *      jql function reference
     *
     * @param string|null $inc
     *
     * @return JqlFunction
     */
    public static function startOfYear($inc = null)
    {
        if ($inc !== null) {
            $inc = JqlQuery::quote($inc);
        }

        return new self("startOfYear($inc)");
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-subtaskIssueTypessubtaskIssueTypes()
     *      jql function reference
     *
     * @return JqlFunction
     */
    public static function subtaskIssueTypes()
    {
        return new self('subtaskIssueTypes()');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-unreleasedVersionsunreleasedVersions()
     *      jql function reference
     *
     * @param string|null $project
     *
     * @return JqlFunction
     */
    public static function unreleasedVersions($project = null)
    {
        return new self("unreleasedVersions($project)");
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-votedIssuesvotedIssues()
     *      jql function reference
     *
     * @return JqlFunction
     */
    public static function votedIssues()
    {
        return new self('votedIssues()');
    }

    /**
     * @see https://confluence.atlassian.com/jiracoreserver073/advanced-searching-functions-reference-861257222.html#Advancedsearching-functionsreference-watchedIssueswatchedIssues()
     *      jql function reference
     *
     * @return JqlFunction
     */
    public static function watchedIssues()
    {
        return new self('watchedIssues()');
    }
}
