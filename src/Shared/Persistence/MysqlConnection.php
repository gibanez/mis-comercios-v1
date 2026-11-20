<?php

declare(strict_types=1);

namespace Gaby\Shared\Persistence;

use Gaby\Shared\Config\Env;
use PDO;

final class MysqlConnection
{
    private static ?PDO $pdo = null;

    public static function create(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $host    = Env::get('DB_HOST', '127.0.0.1') ?? '127.0.0.1';
        $port    = Env::get('DB_PORT', '3306') ?? '3306';
        $name    = Env::get('DB_NAME', 'mis_comercios') ?? 'mis_comercios';
        $user    = Env::get('DB_USER', 'root') ?? 'root';
        $pass    = Env::get('DB_PASS', '') ?? '';

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        self::$pdo = $pdo;

        return $pdo;
    }
}
