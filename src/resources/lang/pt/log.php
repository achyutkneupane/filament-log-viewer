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
        'icon' => 'heroicon-o-document-text',
        'sort' => 100,
    ],
    'table' => [
        'columns' => [
            'log_level' => 'Nível do Log',
            'env' => 'Ambiente',
            'file' => 'Nome do Arquivo',
            'message' => 'Resumo',
            'date' => 'Ocorrência',
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
                'success' => 'Todos os logs foram limpos com sucesso!',
            ],
        ],
    ],
    'schema' => [
        'error-log' => [
            'stack' => 'Rastreamento da Pilha',
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