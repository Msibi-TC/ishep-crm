<?php
declare(strict_types=1);

use Ishep\Config\Environment;
use Ishep\Database\ConnectionFactory;

$base = dirname(__DIR__);
require $base.'/vendor/autoload.php';
Environment::load($base.'/.env');
$required = ['users','password_reset_tokens','roles','permissions','role_permissions','user_roles','provinces','professions','membership_types','audit_logs'];

try {
    $config = require $base.'/config/database.php';
    $pdo = ConnectionFactory::make($config);
    $placeholders = implode(',', array_fill(0, count($required), '?'));
    $tables = $pdo->prepare("SELECT table_name FROM information_schema.tables WHERE table_schema = ? AND table_name IN ($placeholders) ORDER BY table_name");
    $tables->execute(array_merge([$config['database']], $required));
    $found = $tables->fetchAll(PDO::FETCH_COLUMN);
    $missing = array_values(array_diff($required, $found));

    $counts = [];
    foreach (['roles','permissions','provinces','membership_types'] as $table) {
        $statement = $pdo->prepare("SELECT COUNT(*) FROM `$table`");
        $statement->execute();
        $counts[$table] = (int) $statement->fetchColumn();
    }
    $role=$pdo->prepare("SELECT COUNT(*) FROM roles WHERE code = ?");$role->execute(['registered_user']);$registeredRoleCount=(int)$role->fetchColumn();
    $column=$pdo->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = ? AND table_name = 'users' AND column_name = 'membership_type_id'");$column->execute([$config['database']]);$membershipColumnCount=(int)$column->fetchColumn();

    echo 'Tables: '.count($found).'/'.count($required)." present\n";
    foreach ($counts as $table => $count) echo "$table: $count\n";
    echo "registered_user roles: $registeredRoleCount\n";
    echo 'users.membership_type_id: '.($membershipColumnCount===1?'present':'missing')."\n";
    if ($missing) {
        fwrite(STDERR, 'FAIL missing tables: '.implode(', ', $missing)."\n");
        exit(1);
    }
    if ($counts['roles'] < 4 || $counts['permissions'] < 22 || $counts['provinces'] < 9 || $counts['membership_types'] < 3 || $registeredRoleCount!==1 || $membershipColumnCount!==1) {
        fwrite(STDERR, "FAIL reference-data baseline is incomplete\n");
        exit(1);
    }
    echo "PASS schema and reference-data baseline\n";
} catch (Throwable $exception) {
    fwrite(STDERR, 'FAIL schema verification ('.get_class($exception).")\n");
    exit(1);
}
