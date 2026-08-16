<?php

declare(strict_types=1);

return [
    'placeholder' => '-',
    'navigation' => [
        'title' => 'نمایشگر لاگ',
        'heading' => 'جدول لاگ‌ها',
        'subheading' => '',
        'group' => 'سیستم',
        'label' => 'نمایشگر لاگ',
    ],
    'table' => [
        'model_label' => 'گزارش',
        'plural_model_label' => 'گزارش‌ها',
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
                'indicator' => 'فیلتر براساس محیط',
            ],
            'file' => [
                'label' => 'فایل',
                'indicator' => 'فیلتر براساس فایل',
            ],
            'date' => [
                'label' => 'تاریخ',
                'indicator' => 'فیلتر براساس تاریخ',
                'from' => 'از',
                'until' => 'تا',
            ],
            'date_range' => [
                'label' => 'بازه زمانی',
                'indicator' => 'فیلتر براساس بازه زمانی',
            ],
            'indicators' => [
                'logs_from_to' => 'لاگ‌ها از :from تا :until',
                'logs_from' => 'لاگ‌ها از :from',
                'logs_until' => 'لاگ‌ها تا :until',
            ],
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
                'success' => 'تمام لاگ‌ها پاک شدند.',
            ],
            'clear_file' => [
                'label' => 'پاک کردن فایل',
                'modal_heading' => 'پاک کردن :file',
                'modal_description' => 'آیا از پاک کردن لاگ :file مطمئن هستید؟',
                'success' => ':file پاک شد!',
            ],
            'copy_markdown' => [
                'label' => 'کپی به عنوان Markdown',
                'success' => 'Markdown در کلیپبورد کپی شد',
                'headers' => [
                    'file' => 'فایل',
                    'message' => 'پیام',
                    'description' => 'توضیحات',
                    'context' => 'زمینه',
                    'stack_trace' => 'ردیابی پشته',
                    'mail' => 'جزئیات ایمیل',
                ],
            ],
        ],
    ],
    'schema' => [
        'error-log' => [
            'stack' => 'ردیابی پشته',
        ],
        'json-log' => [
            'context' => 'زمینه',
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
        'all' => 'همه لاگ‌ها',
        'alert' => 'هشدار مهم',
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
