<?php

declare(strict_types=1);

return [
    'placeholder' => 'N/D',
    'navigation' => [
        'title' => 'Visualizzatore Log',
        'heading' => 'Tabella dei Log',
        'subheading' => '',
        'group' => 'Sistema',
        'label' => 'Visualizzatore Log',
    ],

    'table' => [
        'columns' => [
            'log_level' => 'Livello Log',
            'env' => 'Ambiente',
            'file' => 'Nome File',
            'message' => 'Riepilogo',
            'date' => 'Data',
        ],
        'filters' => [
            'env' => [
                'label' => 'Ambiente',
                'indicator' => 'Filtrato per ambiente',
            ],
            'file' => [
                'label' => 'File',
                'indicator' => 'Filtrato per file',
            ],
            'date' => [
                'label' => 'Data',
                'indicator' => 'Filtrato per data',
                'from' => 'Da',
                'until' => 'A',
            ],
            'date_range' => [
                'label' => 'Intervallo di date',
                'indicator' => 'Filtrato per intervallo di date',
            ],
            'indicators' => [
                'logs_from_to' => 'Log da :from a :until',
                'logs_from' => 'Log da :from',
                'logs_until' => 'Log fino a :until',
            ],
        ],
        'actions' => [
            'view' => [
                'label' => 'Visualizza',
                'heading' => 'Log di Errore',
            ],
            'read' => [
                'label' => 'Leggi Email',
                'subject' => 'Oggetto',
                'mail_log' => 'Log Email',
                'sent_date' => 'Data di Invio',
            ],
            'refresh' => [
                'label' => 'Aggiorna',
            ],
            'clear' => [
                'label' => 'Pulisci Log',
                'success' => 'Tutti i log sono stati cancellati con successo!',
            ],
        ],
    ],
    'schema' => [
        'error-log' => [
            'stack' => 'Traccia dello Stack',
        ],
        'json-log' => [
            'context' => 'Contesto',
        ],
    ],
    'mail' => [
        'sender' => [
            'label' => 'Mittente',
            'name' => 'Nome',
            'email' => 'Email',
        ],
        'receiver' => [
            'label' => 'Destinatario',
            'name' => 'Nome',
            'email' => 'Email',
        ],
        'content' => 'Contenuto',
        'plain' => 'Testo Semplice',
        'html' => 'HTML',
    ],
    'levels' => [
        'all' => 'Tutti i Log',
        'alert' => 'Allerta',
        'critical' => 'Critico',
        'debug' => 'Debug',
        'emergency' => 'Emergenza',
        'error' => 'Errore',
        'info' => 'Informazione',
        'notice' => 'Avviso',
        'warning' => 'Attenzione',
        'mail' => 'Email',
    ],
];
