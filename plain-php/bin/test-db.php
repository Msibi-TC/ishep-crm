<?php
declare(strict_types=1);

use Ishep\Config\Environment;
use Ishep\Database\ConnectionFactory;

$base = dirname(__DIR__);
require $base.'/vendor/autoload.php';
Environment::load($base.'/.env');

try {
    $config = require $base.'/config/database.php';
    $pdo = ConnectionFactory::make($config);
    $statement = $pdo->prepare('SELECT 1 AS connection_ok');
    $statement->execute();
    $ok = (int) $statement->fetchColumn() === 1;
    echo $ok ? "PASS database connection (PDO MySQL)\n" : "FAIL database connection check\n";
    exit($ok ? 0 : 1);
} catch (Throwable $exception) {
    fwrite(STDERR, 'FAIL database connection ('.get_class($exception).")\n");
    exit(1);
}
