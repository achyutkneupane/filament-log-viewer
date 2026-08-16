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
    ],
    'table' => [
        'model_label' => 'לוג',
        'plural_model_label' => 'לוגים',
        'columns' => [
            'log_level' => 'רמת הלוג',
            'env' => 'סביבה',
            'file' => 'שם הקובץ',
            'message' => 'סיכום',
            'date' => 'זמן התרחשות',
        ],
        'filters' => [
            'env' => [
                'label' => 'סביבה',
                'indicator' => 'מסונן לפי סביבה',
            ],
            'file' => [
                'label' => 'קובץ',
                'indicator' => 'מסונן לפי קובץ',
            ],
            'date' => [
                'label' => 'תאריך',
                'indicator' => 'מסונן לפי תאריך',
                'from' => 'מ־',
                'until' => 'עד',
            ],
            'date_range' => [
                'label' => 'טווח תאריכים',
                'indicator' => 'מסונן לפי טווח תאריכים',
            ],
            'indicators' => [
                'logs_from_to' => 'לוגים מ־:from עד :until',
                'logs_from' => 'לוגים מ־:from',
                'logs_until' => 'לוגים עד :until',
            ],
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
            'clear_file' => [
                'label' => 'ניקוי קובץ',
                'modal_heading' => 'ניקוי :file',
                'modal_description' => 'האם אתה בטוח שברצונך לנקות את קובץ היומן :file؟',
                'success' => ':file נוקה בהצלחה!',
            ],
            'copy_markdown' => [
                'label' => 'העתק כ-Markdown',
                'success' => 'Markdown הועתק ללוח',
                'headers' => [
                    'file' => 'קובץ',
                    'message' => 'הודעה',
                    'description' => 'תיאור',
                    'context' => 'הקשר',
                    'stack_trace' => 'עקבות מחסנית',
                    'mail' => 'פרטי דוא"ל',
                ],
            ],
        ],
    ],
    'schema' => [
        'error-log' => [
            'stack' => 'מעקב מחסנית',
        ],
        'json-log' => [
            'context' => 'קונטקסט',
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
