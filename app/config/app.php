<?php

/**
 * main
 */
return [
    'debug' => 1,
    'clear_twig_cache_on_debug' => 1,
    'clear_doctrine_metadata_cache_on_debug' => 1,
    'admin_email' => 'admin@admin.ua',
    'account_start_page' => '/',
    'login_url' => '/login',
    'not_access_url' => '/error503',
    'session_user_key' => 'ifi3i58i',
    'cache' => 'app\classes\cache\FileCache',
    // 'app\classes\cache\FileCache'
    // 'app\classes\cache\Apcu'
    // 'app\classes\cache\MemCached'
    // 'app\classes\cache\Nocache';
];
