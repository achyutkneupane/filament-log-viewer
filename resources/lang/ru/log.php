<?php

declare(strict_types=1);

return [
    'placeholder' => 'Н/Д',
    'navigation' => [
        'title' => 'Просмотр логов',
        'heading' => 'Таблица логов',
        'subheading' => '',
        'group' => 'Система',
        'label' => 'Просмотр логов',
    ],
    'table' => [
        'model_label' => 'лог',
        'plural_model_label' => 'логи',
        'columns' => [
            'log_level' => 'Уровень логирования',
            'env' => 'Окружение',
            'file' => 'Имя файла',
            'message' => 'Краткое описание',
            'date' => 'Время',
        ],
        'filters' => [
            'env' => [
                'label' => 'Окружение',
                'indicator' => 'Фильтровано по окружению',
            ],
            'file' => [
                'label' => 'Файл',
                'indicator' => 'Фильтровано по файлу',
            ],
            'date' => [
                'label' => 'Дата',
                'indicator' => 'Фильтровано по дате',
                'from' => 'С',
                'until' => 'По',
            ],
            'date_range' => [
                'label' => 'Диапазон дат',
                'indicator' => 'Фильтровано по диапазону дат',
            ],
            'indicators' => [
                'logs_from_to' => 'Логи с :from по :until',
                'logs_from' => 'Логи с :from',
                'logs_until' => 'Логи до :until',
            ],
        ],
        'actions' => [
            'view' => [
                'label' => 'Просмотр',
                'heading' => 'Лог ошибки',
            ],
            'read' => [
                'label' => 'Читать письмо',
                'subject' => 'Тема',
                'mail_log' => 'Лог письма',
                'sent_date' => 'Дата отправки',
            ],
            'refresh' => [
                'label' => 'Обновить',
            ],
            'clear' => [
                'label' => 'Очистить логи',
                'success' => 'Все логи успешно удалены!',
            ],
            'copy_markdown' => [
                'label' => 'Копировать как Markdown',
                'success' => 'Markdown скопирован в буфер обмена',
                'headers' => [
                    'file' => 'Файл',
                    'message' => 'Сообщение',
                    'description' => 'Описание',
                    'context' => 'Контекст',
                    'stack_trace' => 'Трассировка стека',
                    'mail' => 'Детали письма',
                ],
            ],
        ],
    ],
    'schema' => [
        'error-log' => [
            'stack' => 'Трассировка стека',
        ],
        'json-log' => [
            'context' => 'Контекст',
        ],
    ],
    'mail' => [
        'sender' => [
            'label' => 'Отправитель',
            'name' => 'Имя',
            'email' => 'Email',
        ],
        'receiver' => [
            'label' => 'Получатель',
            'name' => 'Имя',
            'email' => 'Email',
        ],
        'content' => 'Содержимое',
        'plain' => 'Обычный текст',
        'html' => 'HTML',
    ],
    'levels' => [
        'all' => 'Все логи',
        'alert' => 'Предупреждение',
        'critical' => 'Критическое',
        'debug' => 'Отладка',
        'emergency' => 'Чрезвычайное',
        'error' => 'Ошибка',
        'info' => 'Информация',
        'notice' => 'Замечание',
        'warning' => 'Предупреждение',
        'mail' => 'Письмо',
    ],
];
