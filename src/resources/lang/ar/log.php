<?php

declare(strict_types=1);

return [
    'placeholder' => 'غير متاح',
    'navigation' => [
        'title' => 'عارض السجلات',
        'heading' => 'جدول السجلات',
        'subheading' => '',
        'group' => 'النظام',
        'label' => 'عارض السجلات',
    ],
    'table' => [
        'columns' => [
            'log_level' => 'مستوى السجل',
            'env' => 'البيئة',
            'file' => 'اسم الملف',
            'message' => 'ملخص',
            'date' => 'وقت الحدوث',
        ],
        'filters' => [
            'env' => [
                'label' => 'البيئة',
                'indicator' => 'مفلتر حسب البيئة',
            ],
            'file' => [
                'label' => 'الملف',
                'indicator' => 'مفلتر حسب الملف',
            ],
            'date' => [
                'label' => 'التاريخ',
                'indicator' => 'مفلتر حسب التاريخ',
                'from' => 'من',
                'until' => 'حتى',
            ],
            'date_range' => [
                'label' => 'النطاق الزمني',
                'indicator' => 'مفلتر حسب النطاق الزمني',
            ],
            'indicators' => [
                'logs_from_to' => 'السجلات من :from إلى :until',
                'logs_from' => 'السجلات من :from',
                'logs_until' => 'السجلات حتى :until',
            ],
        ],
        'actions' => [
            'view' => [
                'label' => 'عرض',
                'heading' => 'سجل الخطأ',
            ],
            'read' => [
                'label' => 'قراءة البريد',
                'subject' => 'الموضوع',
                'mail_log' => 'سجل البريد',
                'sent_date' => 'تاريخ الإرسال',
            ],
            'refresh' => [
                'label' => 'تحديث',
            ],
            'clear' => [
                'label' => 'مسح السجلات',
                'success' => 'تم مسح جميع السجلات بنجاح!',
            ],
        ],
    ],
    'schema' => [
        'error-log' => [
            'stack' => 'تتبع المكدس',
        ],
        'json-log' => [
            'context' => 'السياق',
        ],
    ],
    'mail' => [
        'sender' => [
            'label' => 'المرسل',
            'name' => 'الاسم',
            'email' => 'البريد الإلكتروني',
        ],
        'receiver' => [
            'label' => 'المستلم',
            'name' => 'الاسم',
            'email' => 'البريد الإلكتروني',
        ],
        'content' => 'المحتوى',
        'plain' => 'نص عادي',
        'html' => 'HTML',
    ],
    'levels' => [
        'all' => 'كل السجلات',
        'alert' => 'تنبيه',
        'critical' => 'حرج',
        'debug' => 'تصحيح',
        'emergency' => 'طوارئ',
        'error' => 'خطأ',
        'info' => 'معلومات',
        'notice' => 'ملاحظة',
        'warning' => 'تحذير',
        'mail' => 'بريد',
    ],
];
