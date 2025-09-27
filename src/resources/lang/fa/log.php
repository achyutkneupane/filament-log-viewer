<?php

declare(strict_types=1);

return [
    'placeholder' => '-',
    'navigation' => [
		'title' => 'نمایشگر لاگ',
		'heading' => 'نمایشگر لاگ',
		'subheading' => '',
        'group' => 'سیستم',
        'label' => 'نمایشگر لاگ',
    ],
    'table' => [
        'columns' => [
            'log_level' => 'سطح لاگ',
            'env' => 'محیط',
            'file' => 'نام فایل',
            'message' => 'خلاصه',
            'date' => 'زمان وقوع',
        ],
        'filters' => [
            'env' => [
                'label' => 'محیط',
                'indicator' => 'فیلتر بر اساس محیط',
            ],
            'file' => [
                'label' => 'فایل',
                'indicator' => 'فیلتر بر اساس فایل',
            ],
            'date' => [
                'label' => 'تاریخ',
                'indicator' => 'فیلتر بر اساس تاریخ',
            ],
            'date_range' => [
                'label' => 'بازه زمانی',
                'indicator' => 'فیلتر بر اساس بازه زمانی',
            ],
            'indicators' => [
                'logs_from_to' => 'لاگ‌ها از :start تا :until',
                'logs_from' => 'لاگ‌ها از :start',
                'logs_until' => 'لاگ‌ها تا :until',
            ]
        ],
        'actions' => [
            'view' => [
                'label' => 'مشاهده',
                'heading' => 'لاگ خطا',
            ],
            'read' => [
                'label' => 'خواندن ایمیل',
                'subject' => 'موضوع',
                'mail_log' => 'لاگ ایمیل',
                'sent_date' => 'تاریخ ارسال',
            ],
            'refresh' => [
                'label' => 'تازه‌سازی',
            ],
            'clear' => [
                'label' => 'پاک کردن لاگ‌ها',
                'success' => 'تمام لاگ‌ها با موفقیت پاک شدند!',
            ],
        ],
    ],
    'schema' => [
        'error-log' => [
            'stack' => 'ردیابی پشته',
        ],
    ],
    'mail' => [
        'sender' => [
            'label' => 'فرستنده',
            'name' => 'نام',
            'email' => 'ایمیل',
        ],
        'receiver' => [
            'label' => 'گیرنده',
            'name' => 'نام',
            'email' => 'ایمیل',
        ],
        'content' => 'محتوا',
        'plain' => 'متن ساده',
        'html' => 'HTML',
    ],
    'levels' => [
        'all' => 'تمام لاگ‌ها',
        'alert' => 'هشدار',
        'critical' => 'بحرانی',
        'debug' => 'اشکال‌زدایی',
        'emergency' => 'اضطراری',
        'error' => 'خطا',
        'info' => 'اطلاعات',
        'notice' => 'اطلاعیه',
        'warning' => 'اخطار',
        'mail' => 'ایمیل',
    ],
];