<?php

namespace JiraRestApi;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord;

class NoOperationMonologHandlerV3 extends AbstractProcessingHandler
{
    protected function write(LogRecord $record): void
    {
        // do nothing
    }
}
