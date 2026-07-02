<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\SQLite;

use PDO;

final class SqliteConnection 
{
    private static ?PDO $instance = null;

    private function __construct(){}

    private function __clone(){}


    public static function connect(string $databasePath): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        self::$instance = new PDO(
            dsn: 'sqlite:' . $databasePath,
            options: [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ],
        );

        self::$instance->exec('PRAGMA journal_mode = WAL');
        self::$instance->exec('PRAGMA foreign_keys = ON');
        self::$instance->exec('PRAGMA synchronous = NORMAL');

        return self::$instance;
    }

    public static function runMigrations(PDO $pdo, string $migrationsPath): void
    {
        $files = glob("$migrationsPath/*.sql");

        if ($files === false || $files === []){
                return;
            }

        sort($files, SORT_STRING);

        foreach($files as $file){
                $sql = file_get_contents($file);

                if(!$sql){
                    throw new \RuntimeException(
                        sprintf('Could not read migration file: %s', $file)
                    );
                };

                $pdo->exec($sql);
            }

    }

    public static function reset() : void 
    {
        self::$instance = null;
    }



}