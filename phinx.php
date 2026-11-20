<?php

declare(strict_types=1);

use Gaby\Shared\Config\Env;

require __DIR__ . '/vendor/autoload.php';

return [
    'paths' => [
        'migrations' => __DIR__ . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment'     => 'development',
        'development' => [
            'adapter' => 'mysql',
            'host'    => Env::get('DB_HOST', '127.0.0.1'),
            'port'    => (int) (Env::get('DB_PORT', '3306') ?? '3306'),
            'name'    => Env::get('DB_NAME', 'mis_comercios'),
            'user'    => Env::get('DB_USER', 'root'),
            'pass'    => Env::get('DB_PASS', '') ?? '',
            'charset' => 'utf8mb4',
        ],
    ],
];
