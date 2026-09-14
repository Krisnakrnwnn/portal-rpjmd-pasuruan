<?php

return [
    'otp' => [
        'enabled' => env('ADMIN_OTP_ENABLED', true),
        'expires_minutes' => (int) env('ADMIN_OTP_EXPIRES_MINUTES', 5),
        'max_attempts' => (int) env('ADMIN_OTP_MAX_ATTEMPTS', 5),
        'resend_cooldown_seconds' => (int) env('ADMIN_OTP_RESEND_COOLDOWN_SECONDS', 60),
        'max_deliveries' => (int) env('ADMIN_OTP_MAX_DELIVERIES', 3),
        'delivery_window_seconds' => (int) env('ADMIN_OTP_DELIVERY_WINDOW_SECONDS', 900),
        'ip_max_deliveries' => (int) env('ADMIN_OTP_IP_MAX_DELIVERIES', 10),
        'retention_hours' => (int) env('ADMIN_OTP_RETENTION_HOURS', 24),
    ],

    'support_email' => env('ADMIN_SUPPORT_EMAIL', env('MAIL_FROM_ADDRESS')),
];
