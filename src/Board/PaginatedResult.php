<?php

namespace JiraRestApi\Board;

/**
 * Paginated Result object for BoardService
 */
class PaginatedResult
{
    /**
     * @var string
     */
    public $expand;

    /**
     * @var int
     */
    public $startAt;

    /**
     * @var int
     */
    public $maxResults;

    /**
     * @var int
     */
    public $total;

    /**
     * @var array
     */
    public $values;

    /**
     * @var bool
     */
    public $isLast;

    /**
     * @return int
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * @param int $startAt
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;
    }

    /**
     * @return int
     */
    public function getMaxResults()
    {
        return $this->maxResults;
    }

    /**
     * @param int $maxResults
     */
    public function setMaxResults($maxResults)
    {
        $this->maxResults = $maxResults;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param array $values
     */
    public function setValues($values)
    {
        $this->values = $values;
    }

    /**
     * @param int $index
     *
     * @return mixed
     */
    public function getValue($index)
    {
        return $this->values[$index];
    }

    /**
     * @return string
     */
    public function getExpand()
    {
        return $this->expand;
    }

    /**
     * @param string $expand
     */
    public function setExpand($expand)
    {
        $this->expand = $expand;
    }
}
