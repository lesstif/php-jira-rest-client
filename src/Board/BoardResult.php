<?php

namespace JiraRestApi\Board;

/**
 * Result object for BoardService::getBoards()
 */
class BoardResult
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
     * @var \JiraRestApi\Board\Board[]
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
     * @return Board[]
     */
    public function getBoards()
    {
        return $this->values;
    }

    /**
     * @param Board[] $boards
     */
    public function setBoards($boards)
    {
        $this->values = $boards;
    }

    /**
     * @param int $index
     *
     * @return Board
     */
    public function getBoard($index)
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
