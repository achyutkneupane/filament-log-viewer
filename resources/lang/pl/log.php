<?php

declare(strict_types=1);

return [
    'placeholder' => 'N/A',
    'navigation' => [
        'title' => 'Przeglądarka logów',
        'heading' => 'Tabela logów',
        'subheading' => '',
        'group' => 'System',
        'label' => 'Przeglądarka logów',
    ],
    'table' => [
        'model_label' => 'log',
        'plural_model_label' => 'logi',
        'columns' => [
            'log_level' => 'Poziom',
            'env' => 'Środowisko',
            'file' => 'Nazwa pliku',
            'message' => 'Wiadomość',
            'date' => 'Wystąpił',
        ],
        'filters' => [
            'env' => [
                'label' => 'Środowisko',
                'indicator' => 'Filtrowane po środowisku',
            ],
            'file' => [
                'label' => 'Plik',
                'indicator' => 'Filtrowane po pliku',
            ],
            'date' => [
                'label' => 'Data',
                'indicator' => 'Filtrowane po dacie',
                'from' => 'Od',
                'until' => 'Do',
            ],
            'date_range' => [
                'label' => 'Przedział czasowy',
                'indicator' => 'Filtrowane po zakresie dat',
            ],
            'indicators' => [
                'logs_from_to' => 'Logi od :from do :until',
                'logs_from' => 'Logi od :from',
                'logs_until' => 'Logi do :until',
            ],
        ],
        'actions' => [
            'view' => [
                'label' => 'Podgląd',
                'heading' => 'Dziennik błędu',
            ],
            'read' => [
                'label' => 'Podgląd wiadomości',
                'subject' => 'Temat',
                'mail_log' => 'Log poczty',
                'sent_date' => 'Data wysłania',
            ],
            'refresh' => [
                'label' => 'Odśwież',
            ],
            'clear' => [
                'label' => 'Wyczyść logi',
                'success' => 'Wszystkie logi zostały wyczyszczone!',
            ],
            'clear_file' => [
                'label' => 'Usuń zawartość pliku',
                'modal_heading' => 'Usuń zawartość pliku :file',
                'modal_description' => 'Czy na pewno chcesz usunąć zawartość pliku :file?',
                'success' => 'Zawartość pliku :file została usunięta!',
            ],
            'copy_markdown' => [
                'label' => 'Kopiuj jako Markdown',
                'success' => 'Markdown skopiowany do schowka',
                'headers' => [
                    'file' => 'Plik',
                    'message' => 'Wiadomość',
                    'description' => 'Opis',
                    'context' => 'Kontekst',
                    'stack_trace' => 'Ślad stosu',
                    'mail' => 'Szczegóły wiadomości',
                ],
            ],
        ],
    ],
    'schema' => [
        'error-log' => [
            'stack' => 'Ślad stosu',
        ],
        'json-log' => [
            'context' => 'Kontekst',
        ],
    ],
    'mail' => [
        'sender' => [
            'label' => 'Nadawca',
            'name' => 'Nazwa',
            'email' => 'E-mail',
        ],
        'receiver' => [
            'label' => 'Odbiorca',
            'name' => 'Nazwa',
            'email' => 'E-mail',
        ],
        'content' => 'Zawartość',
        'plain' => 'Tekst',
        'html' => 'HTML',
    ],
    'levels' => [
        'all' => 'Wszystkie logi',
        'alert' => 'Alarm',
        'critical' => 'Krytyczny',
        'debug' => 'Debug',
        'emergency' => 'Sytuacja krytyczna',
        'error' => 'Błąd',
        'info' => 'Info',
        'notice' => 'Uwaga',
        'warning' => 'Ostrzeżenie',
        'mail' => 'E-mail',
    ],
];
