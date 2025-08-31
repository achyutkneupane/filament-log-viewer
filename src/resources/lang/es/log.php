<?php

declare(strict_types=1);

return [
    'placeholder' => 'N/D',
    'navigation' => [
        'title' => 'Visor de Logs',
        'heading' => 'Tabla de Logs',
        'subheading' => '',
        'group' => 'Sistema',
        'label' => 'Visor de Logs',
        'icon' => 'heroicon-o-document-text',
        'sort' => 100,
    ],
    'table' => [
        'columns' => [
            'log_level' => 'Nivel de Log',
            'env' => 'Entorno',
            'file' => 'Nombre del Archivo',
            'message' => 'Resumen',
            'date' => 'Ocurrió',
        ],
        'actions' => [
            'view' => [
                'label' => 'Ver',
                'heading' => 'Log de Error',
            ],
            'read' => [
                'label' => 'Leer Correo',
                'subject' => 'Asunto',
                'mail_log' => 'Log de Correo',
                'sent_date' => 'Fecha de Envío',
            ],
            'refresh' => [
                'label' => 'Actualizar',
            ],
            'clear' => [
                'label' => 'Limpiar Logs',
                'success' => '¡Todos los logs han sido limpiados!',
            ],
        ],
    ],
    'schema' => [
        'error-log' => [
            'stack' => 'Rastreo de Pila',
        ],
    ],
    'mail' => [
        'sender' => [
            'label' => 'Remitente',
            'name' => 'Nombre',
            'email' => 'Correo Electrónico',
        ],
        'receiver' => [
            'label' => 'Destinatario',
            'name' => 'Nombre',
            'email' => 'Correo Electrónico',
        ],
        'content' => 'Contenido',
        'plain' => 'Texto Plano',
        'html' => 'HTML',
    ],
    'levels' => [
        'all' => 'Todos los Logs',
        'alert' => 'Alerta',
        'critical' => 'Crítico',
        'debug' => 'Depuración',
        'emergency' => 'Emergencia',
        'error' => 'Error',
        'info' => 'Información',
        'notice' => 'Aviso',
        'warning' => 'Advertencia',
        'mail' => 'Correo',
    ],
];