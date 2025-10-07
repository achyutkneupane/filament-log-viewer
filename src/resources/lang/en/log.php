<?php

declare(strict_types=1);

return [
    'placeholder' => 'N/A',
    'navigation' => [
        'title' => 'Log Viewer',
        'heading' => 'Log Table',
        'subheading' => '',
        'group' => 'System',
        'label' => 'Log Viewer',
    ],
    'table' => [
        'columns' => [
            'log_level' => 'Log Level',
            'env' => 'Environment',
            'file' => 'File Name',
            'message' => 'Summary',
            'date' => 'Occurred',
        ],
        'filters' => [
            'env' => [
                'label' => 'Environment',
                'indicator' => 'Filtered by environment',
            ],
            'file' => [
                'label' => 'File',
                'indicator' => 'Filtered by file',
            ],
            'date' => [
                'label' => 'Date',
                'indicator' => 'Filtered by date',
                'from' => 'From',
                'until' => 'Until',
            ],
            'date_range' => [
                'label' => 'Date Range',
                'indicator' => 'Filtered by date range',
            ],
            'indicators' => [
                'logs_from_to' => 'Logs from :from to :until',
                'logs_from' => 'Logs from :from',
                'logs_until' => 'Logs until :until',
            ],
        ],
        'actions' => [
            'view' => [
                'label' => 'View',
                'heading' => 'Error Log',
            ],
            'read' => [
                'label' => 'Read Mail',
                'subject' => 'Subject',
                'mail_log' => 'Mail Log',
                'sent_date' => 'Sent Date',
            ],
            'refresh' => [
                'label' => 'Refresh',
            ],
            'clear' => [
                'label' => 'Clear Logs',
                'success' => 'All logs have been cleared!',
            ],
        ],
    ],
    'schema' => [
        'error-log' => [
            'stack' => 'Stack Trace',
        ],
        'json-log' => [
            'context' => 'Context',
        ],
    ],
    'mail' => [
        'sender' => [
            'label' => 'Sender',
            'name' => 'Name',
            'email' => 'Email',
        ],
        'receiver' => [
            'label' => 'Receiver',
            'name' => 'Name',
            'email' => 'Email',
        ],
        'content' => 'Content',
        'plain' => 'Plain Text',
        'html' => 'HTML',
    ],
    'levels' => [
        'all' => 'All Logs',
        'alert' => 'Alert',
        'critical' => 'Critical',
        'debug' => 'Debug',
        'emergency' => 'Emergency',
        'error' => 'Error',
        'info' => 'Info',
        'notice' => 'Notice',
        'warning' => 'Warning',
        'mail' => 'Mail',
    ],
];
