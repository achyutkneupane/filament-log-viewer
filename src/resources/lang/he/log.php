<?php

declare(strict_types=1);

return [
    'placeholder' => 'לא זמין',
    'navigation' => [
        'title' => 'מציג לוגים',
        'heading' => 'טבלת לוגים',
        'subheading' => '',
        'group' => 'מערכת',
        'label' => 'מציג לוגים',
        'icon' => 'heroicon-o-document-text',
        'sort' => 100,
    ],
    'table' => [
        'columns' => [
            'log_level' => 'רמת הלוג',
            'env' => 'סביבה',
            'file' => 'שם הקובץ',
            'message' => 'סיכום',
            'date' => 'זמן התרחשות',
        ],
        'actions' => [
            'view' => [
                'label' => 'הצג',
                'heading' => 'לוג שגיאה',
            ],
            'read' => [
                'label' => 'קרא דואר',
                'subject' => 'נושא',
                'mail_log' => 'לוג דואר',
                'sent_date' => 'תאריך שליחה',
            ],
            'refresh' => [
                'label' => 'רענן',
            ],
            'clear' => [
                'label' => 'נקה לוגים',
                'success' => 'כל הלוגים נוקו בהצלחה!',
            ],
        ],
    ],
    'schema' => [
        'error-log' => [
            'stack' => 'מעקב מחסנית',
        ],
    ],
    'mail' => [
        'sender' => [
            'label' => 'שולח',
            'name' => 'שם',
            'email' => 'דוא"ל',
        ],
        'receiver' => [
            'label' => 'נמען',
            'name' => 'שם',
            'email' => 'דוא"ל',
        ],
        'content' => 'תוכן',
        'plain' => 'טקסט רגיל',
        'html' => 'HTML',
    ],
    'levels' => [
        'all' => 'כל הלוגים',
        'alert' => 'התראה',
        'critical' => 'קריטי',
        'debug' => 'דיבאג',
        'emergency' => 'חירום',
        'error' => 'שגיאה',
        'info' => 'מידע',
        'notice' => 'הודעה',
        'warning' => 'אזהרה',
        'mail' => 'דואר',
    ],
];