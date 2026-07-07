<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use PDO;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $connection = config('database.default');
        $config = config("database.connections.{$connection}");
        $database = $config['database'];

        $pdo = new PDO(
            sprintf('mysql:host=%s;port=%s', $config['host'], $config['port'] ?? 3306),
            $config['username'],
            $config['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => true,
            ]
        );

        $this->importSchemaAndData($pdo, $database);
        $pdo->exec("USE `{$database}`;");
        $this->importStoredProcedures($pdo);
    }

    private function importSchemaAndData(PDO $pdo, string $database): void
    {
        $sql = file_get_contents(database_path('createscript.sql'));

        if ($sql === false) {
            throw new \RuntimeException('Could not read database/createscript.sql.');
        }

        $sql = str_replace('kniploket_tiko', $database, $sql);
        $sql = str_replace('SET FOREIGN_KEY_CHECKS = 1;', '-- SET FOREIGN_KEY_CHECKS = 1;', $sql);

        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0;');
        $pdo->exec($sql);
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1;');
    }

    private function importStoredProcedures(PDO $pdo): void
    {
        foreach (glob(database_path('stored-procedures/*.sql')) ?: [] as $file) {
            $sql = file_get_contents($file);

            if ($sql === false) {
                throw new \RuntimeException("Could not read stored procedure file: {$file}");
            }

            $pdo->exec($this->normalizeProcedureSql($sql));
        }
    }

    private function normalizeProcedureSql(string $sql): string
    {
        $lines = [];

        foreach (explode("\n", $sql) as $line) {
            $trimmed = trim($line);

            if (stripos($trimmed, 'DELIMITER') === 0) {
                continue;
            }

            if (str_ends_with($trimmed, '//')) {
                $line = substr($line, 0, strrpos($line, '//')).';';
            }

            $lines[] = $line;
        }

        return implode("\n", $lines);
    }
}
