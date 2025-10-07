<?php

declare(strict_types=1);

return [
    'placeholder' => 'N/D',
    'navigation' => [
        'title' => 'Visualizador de Logs',
        'heading' => 'Tabela de Logs',
        'subheading' => '',
        'group' => 'Sistema',
        'label' => 'Visualizador de Logs',
    ],
    'table' => [
        'columns' => [
            'log_level' => 'Nível do Log',
            'env' => 'Ambiente',
            'file' => 'Nome do Arquivo',
            'message' => 'Resumo',
            'date' => 'Ocorrido em',
        ],
        'filters' => [
            'env' => [
                'label' => 'Ambiente',
                'indicator' => 'Filtrado por ambiente',
            ],
            'file' => [
                'label' => 'Arquivo',
                'indicator' => 'Filtrado por arquivo',
            ],
            'date' => [
                'label' => 'Data',
                'indicator' => 'Filtrado por data',
                'from' => 'De',
                'until' => 'Até',
            ],
            'date_range' => [
                'label' => 'Período',
                'indicator' => 'Filtrado por período',
            ],
            'indicators' => [
                'logs_from_to' => 'Logs de :from até :until',
                'logs_from' => 'Logs a partir de :from',
                'logs_until' => 'Logs até :until',
            ],
        ],
        'actions' => [
            'view' => [
                'label' => 'Visualizar',
                'heading' => 'Log de Erro',
            ],
            'read' => [
                'label' => 'Ler E-mail',
                'subject' => 'Assunto',
                'mail_log' => 'Log de E-mail',
                'sent_date' => 'Data de Envio',
            ],
            'refresh' => [
                'label' => 'Atualizar',
            ],
            'clear' => [
                'label' => 'Limpar Logs',
                'success' => 'Todos os logs foram limpos!',
            ],
        ],
    ],
    'schema' => [
        'error-log' => [
            'stack' => 'Rastreamento da Pilha',
        ],
        'json-log' => [
            'context' => 'Contexto',
        ],
    ],
    'mail' => [
        'sender' => [
            'label' => 'Remetente',
            'name' => 'Nome',
            'email' => 'E-mail',
        ],
        'receiver' => [
            'label' => 'Destinatário',
            'name' => 'Nome',
            'email' => 'E-mail',
        ],
        'content' => 'Conteúdo',
        'plain' => 'Texto Simples',
        'html' => 'HTML',
    ],
    'levels' => [
        'all' => 'Todos os Logs',
        'alert' => 'Alerta',
        'critical' => 'Crítico',
        'debug' => 'Depuração',
        'emergency' => 'Emergência',
        'error' => 'Erro',
        'info' => 'Informação',
        'notice' => 'Aviso',
        'warning' => 'Atenção',
        'mail' => 'E-mail',
    ],
];
