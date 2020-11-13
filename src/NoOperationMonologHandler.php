<?php

declare(strict_types=1);

namespace JiraRestApi;

use Monolog\Handler\AbstractProcessingHandler;

class NoOperationMonologHandler extends AbstractProcessingHandler
{
    /**
     * Writes the record down to the log of the implementing handler.
     *
     * @param array $record
     *
     * @return void
     */
    protected function write(array $record): void
    {
        // do nothing
    }
}
