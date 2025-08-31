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
        'icon' => 'heroicon-o-document-text',
        'sort' => 100,
    ],
    'table' => [
        'columns' => [
            'log_level' => 'سطح لاگ',
            'env' => 'محیط',
            'file' => 'نام فایل',
            'message' => 'خلاصه',
            'date' => 'زمان وقوع',
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