<?php

try {
    $host = '127.0.0.1';
    $port = 3306;
    $username = 'root';
    $password = '';
    $sqlFile = __DIR__ . '/createscript.sql';
    $proceduresDir = __DIR__ . '/stored-procedures';

    echo "Connecting to MySQL at $host:$port...\n";
    $pdo = new PDO("mysql:host=$host;port=$port", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => true,
    ]);

    // 1. Importeer basis schema en testdata voor kniploket_tiko
    echo "Executing SQL script for kniploket_tiko...\n";
    $sql = file_get_contents($sqlFile);
    if ($sql === false) {
        throw new Exception("Could not read SQL file: $sqlFile");
    }
    $pdo->exec($sql);
    echo "Database kniploket_tiko imported successfully!\n";

    // Selecteer de kniploket_tiko database en importeer Stored Procedures
    $pdo->exec("USE kniploket_tiko;");
    importProcedures($pdo, $proceduresDir);

    // 2. Importeer basis schema en testdata voor kerentaak_ex_test
    echo "Executing SQL script for kerentaak_ex_test...\n";
    $testSql = str_replace('kniploket_tiko', 'kerentaak_ex_test', $sql);
    $pdo->exec($testSql);
    echo "Database kerentaak_ex_test imported successfully!\n";

    // Selecteer de testdatabase en importeer Stored Procedures
    $pdo->exec("USE kerentaak_ex_test;");
    importProcedures($pdo, $proceduresDir);

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

/**
 * Scant de map met stored procedures, verwijdert DELIMITER commando's (die niet door PDO worden ondersteund)
 * en voert alle .sql-bestanden uit op de huidige MySQL connectie.
 */
function importProcedures($pdo, $dir) {
    if (!is_dir($dir)) {
        return;
    }
    echo "Importing stored procedures from " . basename($dir) . "...\n";
    $files = glob($dir . '/*.sql');
    foreach ($files as $file) {
        $procSql = file_get_contents($file);
        if ($procSql !== false) {
            echo "Executing procedure script: " . basename($file) . "\n";
            
            // Regel-voor-regel verwerking om DELIMITER regels te strippen en // te vervangen door semicolons
            $lines = explode("\n", $procSql);
            $cleanLines = [];
            foreach ($lines as $line) {
                $trimmed = trim($line);
                
                // Sla de DELIMITER regels over (deze zijn alleen voor CLI tools / MySQL Workbench)
                if (stripos($trimmed, 'DELIMITER') === 0) {
                    continue;
                }
                
                // Vervang de // delimiter aan het einde van een statement door een reguliere puntkomma
                if (str_ends_with($trimmed, '//')) {
                    $line = substr($line, 0, strrpos($line, '//')) . ';';
                }
                
                $cleanLines[] = $line;
            }
            $cleanSql = implode("\n", $cleanLines);
            
            $pdo->exec($cleanSql);
        }
    }
}
