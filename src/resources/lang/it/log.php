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
        'icon' => 'heroicon-o-document-text',
        'sort' => 100,
    ],
    'table' => [
        'columns' => [
            'log_level' => 'Livello Log',
            'env' => 'Ambiente',
            'file' => 'Nome File',
            'message' => 'Riepilogo',
            'date' => 'Data',
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