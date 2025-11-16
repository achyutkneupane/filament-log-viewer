<?php

declare(strict_types=1);

return [
    'placeholder' => 'N/A',
    'navigation' => [
        'title' => 'Visionneur de Logs',
        'heading' => 'Tableau des Logs',
        'subheading' => '',
        'group' => 'Système',
        'label' => 'Visionneur de Logs',
    ],
    'table' => [
        'columns' => [
            'log_level' => 'Niveau de Log',
            'env' => 'Environnement',
            'file' => 'Nom du Fichier',
            'message' => 'Résumé',
            'date' => 'Survenu le',
        ],
        'filters' => [
            'env' => [
                'label' => 'Environnement',
                'indicator' => 'Filtré par environnement',
            ],
            'file' => [
                'label' => 'Fichier',
                'indicator' => 'Filtré par fichier',
            ],
            'date' => [
                'label' => 'Date',
                'indicator' => 'Filtré par date',
                'from' => 'De',
                'until' => 'Jusqu\'à',
            ],
            'date_range' => [
                'label' => 'Plage de dates',
                'indicator' => 'Filtré par plage de dates',
            ],
            'indicators' => [
                'logs_from_to' => 'Journaux du :from au :until',
                'logs_from' => 'Journaux à partir du :from',
                'logs_until' => 'Journaux jusqu\'au :until',
            ],
        ],
        'actions' => [
            'view' => [
                'label' => 'Voir',
                'heading' => 'Log d\'Erreur',
            ],
            'read' => [
                'label' => 'Lire l\'Email',
                'subject' => 'Sujet',
                'mail_log' => 'Log d\'Email',
                'sent_date' => 'Date d\'envoi',
            ],
            'refresh' => [
                'label' => 'Actualiser',
            ],
            'clear' => [
                'label' => 'Vider les Logs',
                'success' => 'Tous les logs ont été vidés avec succès !',
            ],
        ],
    ],
    'schema' => [
        'error-log' => [
            'stack' => 'Trace de la Pile',
        ],
        'json-log' => [
            'context' => 'Contexte',
        ],
    ],
    'mail' => [
        'sender' => [
            'label' => 'Expéditeur',
            'name' => 'Nom',
            'email' => 'Email',
        ],
        'receiver' => [
            'label' => 'Destinataire',
            'name' => 'Nom',
            'email' => 'Email',
        ],
        'content' => 'Contenu',
        'plain' => 'Texte Brut',
        'html' => 'HTML',
    ],
    'levels' => [
        'all' => 'Tous les Logs',
        'alert' => 'Alerte',
        'critical' => 'Critique',
        'debug' => 'Débogage',
        'emergency' => 'Urgence',
        'error' => 'Erreur',
        'info' => 'Information',
        'notice' => 'Notice',
        'warning' => 'Avertissement',
        'mail' => 'Email',
    ],
];
