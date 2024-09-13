<?php

namespace JiraRestApi;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

class NoOperationMonologHandler extends AbstractProcessingHandler
{
    /**
     * Writes the record down to the log of the implementing handler.
     *
     * @param array $record
     *
     * @return void
     */
    protected function write(LogRecord $record): void
    {
        // do nothing
    }
}
