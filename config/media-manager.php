<?php

return [
    // Storage disk to use (default: public)
    'disk' => env('MEDIA_MANAGER_DISK', 'public'),

    // Allowed file types (MIME types or extensions)
    'allowed_types' => [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        // Add more as needed
    ],

    // Max file size in kilobytes (default: 5MB)
    'max_file_size' => env('MEDIA_MANAGER_MAX_FILE_SIZE', 5120),

    // Max total size for all files (in bytes)
    'max_total_size' => env('MEDIA_MANAGER_MAX_TOTAL_SIZE', 1073741824), // 1GB by default
];