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
    ],
    'table' => [
        'columns' => [
            'log_level' => 'Nivel de Log',
            'env' => 'Entorno',
            'file' => 'Nombre del Archivo',
            'message' => 'Resumen',
            'date' => 'Ocurrió',
        ],
        'filters' => [
            'env' => [
                'label' => 'Entorno',
                'indicator' => 'Filtrado por entorno',
            ],
            'file' => [
                'label' => 'Archivo',
                'indicator' => 'Filtrado por archivo',
            ],
            'date' => [
                'label' => 'Fecha',
                'indicator' => 'Filtrado por fecha',
                'from' => 'Desde',
                'until' => 'Hasta',
            ],
            'date_range' => [
                'label' => 'Rango de Fechas',
                'indicator' => 'Filtrado por rango de fechas',
            ],
            'indicators' => [
                'logs_from_to' => 'Logs desde :from hasta :until',
                'logs_from' => 'Logs desde :from',
                'logs_until' => 'Logs hasta :until',
            ],
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
        'json-log' => [
            'context' => 'Contexto',
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
