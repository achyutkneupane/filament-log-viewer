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
        'icon' => 'heroicon-o-document-text',
        'sort' => 100,
    ],
    'table' => [
        'columns' => [
            'log_level' => 'Log-Level',
            'env' => 'Umgebung',
            'file' => 'Dateiname',
            'message' => 'Zusammenfassung',
            'date' => 'Aufgetreten',
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