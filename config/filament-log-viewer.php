<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Maximum Log File Size
    |--------------------------------------------------------------------------
    |
    | The maximum size (in kilobytes) for a single log file to be read.
    | Files larger than this will be skipped to prevent memory exhaustion.
    */
    'max_log_file_size' => (int) env('LOG_MAX_SIZE_KB', 2048),

    /*
    |--------------------------------------------------------------------------
    | Enable Log Deletion
    |--------------------------------------------------------------------------
    |
    | Whether to allow deletion of log files through the interface. Set to false
    | to disable this feature and prevent accidental log file removal.
    */
    'enable_delete' => env('LOG_ENABLE_DELETE', true),

    /*
    |--------------------------------------------------------------------------
    | Enable Copy as Markdown
    |--------------------------------------------------------------------------
    |
    | Whether to allow copying log entries as Markdown. Set to false to disable
    | this feature.
    */
    'enable_copy_markdown' => env('LOG_ENABLE_COPY_MARKDOWN', true),

    /*
    |--------------------------------------------------------------------------
    | Disable Parsed Row Cache
    |--------------------------------------------------------------------------
    |
    | Whether to skip caching parsed log rows between requests. The cache is
    | keyed by a fingerprint of the log files, so it self-invalidates whenever
    | a file changes. Set to true to disable it entirely.
    */
    'disable_cache' => (bool) env('LOG_DISABLE_CACHE', false),

    /*
    |--------------------------------------------------------------------------
    | Copy as Markdown Log Levels
    |--------------------------------------------------------------------------
    |
    | The log levels that will allow copying as Markdown. Only logs of these levels
    | will show the "Copy as Markdown" action. Defaults to 'error'.
    */
    'copy_markdown_levels' => explode(',', env('LOG_COPY_MARKDOWN_LEVELS', 'error')),
];
