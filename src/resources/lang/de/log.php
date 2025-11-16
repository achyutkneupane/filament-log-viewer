<?php

declare(strict_types=1);

return [
    'placeholder' => 'N/A',
    'navigation' => [
        'title' => 'Log-Anzeige',
        'heading' => 'Log-Tabelle',
        'subheading' => '',
        'group' => 'System',
        'label' => 'Log-Anzeige',
    ],
    'table' => [
        'columns' => [
            'log_level' => 'Log-Level',
            'env' => 'Umgebung',
            'file' => 'Dateiname',
            'message' => 'Zusammenfassung',
            'date' => 'Aufgetreten',
        ],
        'filters' => [
            'env' => [
                'label' => 'Umgebung',
                'indicator' => 'Gefiltert nach Umgebung',
            ],
            'file' => [
                'label' => 'Datei',
                'indicator' => 'Gefiltert nach Datei',
            ],
            'date' => [
                'label' => 'Datum',
                'indicator' => 'Gefiltert nach Datum',
                'from' => 'Von',
                'until' => 'Bis',
            ],
            'date_range' => [
                'label' => 'Zeitraum',
                'indicator' => 'Gefiltert nach Zeitraum',
            ],
            'indicators' => [
                'logs_from_to' => 'Logs von :from bis :until',
                'logs_from' => 'Logs ab :from',
                'logs_until' => 'Logs bis :until',
            ],
        ],
        'actions' => [
            'view' => [
                'label' => 'Ansehen',
                'heading' => 'Fehler-Log',
            ],
            'read' => [
                'label' => 'E-Mail lesen',
                'subject' => 'Betreff',
                'mail_log' => 'E-Mail-Log',
                'sent_date' => 'Sendedatum',
            ],
            'refresh' => [
                'label' => 'Aktualisieren',
            ],
            'clear' => [
                'label' => 'Logs löschen',
                'success' => 'Alle Logs wurden erfolgreich gelöscht!',
            ],
        ],
    ],
    'schema' => [
        'error-log' => [
            'stack' => 'Stack-Trace',
        ],
        'json-log' => [
            'context' => 'Kontext',
        ],
    ],
    'mail' => [
        'sender' => [
            'label' => 'Absender',
            'name' => 'Name',
            'email' => 'E-Mail',
        ],
        'receiver' => [
            'label' => 'Empfänger',
            'name' => 'Name',
            'email' => 'E-Mail',
        ],
        'content' => 'Inhalt',
        'plain' => 'Klartext',
        'html' => 'HTML',
    ],
    'levels' => [
        'all' => 'Alle Logs',
        'alert' => 'Alarm',
        'critical' => 'Kritisch',
        'debug' => 'Debug',
        'emergency' => 'Notfall',
        'error' => 'Fehler',
        'info' => 'Info',
        'notice' => 'Hinweis',
        'warning' => 'Warnung',
        'mail' => 'Mail',
    ],
];
