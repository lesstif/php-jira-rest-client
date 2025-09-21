<?php

namespace JiraRestApi\Issue;

/**
 * JQL Count result for approximate count API.
 */
class JQLCountResult implements \JsonSerializable
{
    /**
     * @var int The approximate count of issues matching the JQL query
     */
    public int $count;

    /**
     * Get the count of issues.
     *
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Set the count of issues.
     *
     * @param int $count
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'count' => $this->count,
        ];
    }
}
