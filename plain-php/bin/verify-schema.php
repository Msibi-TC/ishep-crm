<?php
declare(strict_types=1);

use Ishep\Config\Environment;
use Ishep\Database\ConnectionFactory;

$base = dirname(__DIR__);
require $base.'/vendor/autoload.php';
Environment::load($base.'/.env');
$required = ['users','password_reset_tokens','roles','permissions','role_permissions','user_roles','provinces','professions','membership_types','audit_logs','member_profiles','membership_applications','membership_application_events','document_types','membership_document_requirements','member_documents','member_document_events'];

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
    $profileColumns=['id','user_id','telephone','province_id','profession_id','organisation','job_title','biography','created_at','updated_at'];
    $columns=$pdo->prepare("SELECT column_name FROM information_schema.columns WHERE table_schema=? AND table_name='member_profiles'");$columns->execute([$config['database']]);$missingProfileColumns=array_values(array_diff($profileColumns,$columns->fetchAll(PDO::FETCH_COLUMN)));
    $unique=$pdo->prepare("SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema=? AND table_name='member_profiles' AND column_name='user_id' AND non_unique=0");$unique->execute([$config['database']]);$profileUserUnique=(int)$unique->fetchColumn()>=1;
    $applicationColumns=['id','application_number','user_id','membership_type_id','status','declaration_at','submitted_at','reviewed_by','review_started_at','decided_at','decision_reason','internal_note','created_at','updated_at'];$columns=$pdo->prepare("SELECT column_name FROM information_schema.columns WHERE table_schema=? AND table_name='membership_applications'");$columns->execute([$config['database']]);$missingApplicationColumns=array_values(array_diff($applicationColumns,$columns->fetchAll(PDO::FETCH_COLUMN)));
    $eventColumns=['id','membership_application_id','actor_user_id','previous_status','new_status','note','created_at'];$columns=$pdo->prepare("SELECT column_name FROM information_schema.columns WHERE table_schema=? AND table_name='membership_application_events'");$columns->execute([$config['database']]);$missingEventColumns=array_values(array_diff($eventColumns,$columns->fetchAll(PDO::FETCH_COLUMN)));
    $index=$pdo->prepare("SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema=? AND table_name='membership_applications' AND index_name='membership_applications_number_unique' AND non_unique=0");$index->execute([$config['database']]);$applicationNumberUnique=(int)$index->fetchColumn()===1;
    $documentColumns=['document_reference','user_id','membership_application_id','document_type_id','original_filename','storage_key','relative_path','detected_mime','extension','byte_size','sha256','verification_status','uploaded_at','verified_at','verified_by','rejection_reason','internal_note','replaced_document_id'];$columns=$pdo->prepare("SELECT column_name FROM information_schema.columns WHERE table_schema=? AND table_name='member_documents'");$columns->execute([$config['database']]);$missingDocumentColumns=array_values(array_diff($documentColumns,$columns->fetchAll(PDO::FETCH_COLUMN)));$docIndex=$pdo->prepare("SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema=? AND table_name='member_documents' AND index_name='member_documents_reference_unique' AND non_unique=0");$docIndex->execute([$config['database']]);$documentReferenceUnique=(int)$docIndex->fetchColumn()===1;$generic=$pdo->prepare("SELECT COUNT(*) FROM document_types WHERE code='supporting_document' AND active=1");$generic->execute();$genericDocumentType=(int)$generic->fetchColumn()===1;

    echo 'Tables: '.count($found).'/'.count($required)." present\n";
    foreach ($counts as $table => $count) echo "$table: $count\n";
    echo "registered_user roles: $registeredRoleCount\n";
    echo 'users.membership_type_id: '.($membershipColumnCount===1?'present':'missing')."\n";
    echo 'member_profiles critical columns: '.($missingProfileColumns?'missing '.implode(', ',$missingProfileColumns):'present')."\n";
    echo 'member_profiles.user_id unique: '.($profileUserUnique?'yes':'no')."\n";
    echo 'membership application columns: '.($missingApplicationColumns?'missing '.implode(', ',$missingApplicationColumns):'present')."\n";echo 'membership event columns: '.($missingEventColumns?'missing '.implode(', ',$missingEventColumns):'present')."\n";echo 'application number unique: '.($applicationNumberUnique?'yes':'no')."\n";
    echo 'member document columns: '.($missingDocumentColumns?'missing '.implode(', ',$missingDocumentColumns):'present')."\n";echo 'document reference unique: '.($documentReferenceUnique?'yes':'no')."\n";echo 'generic optional document type: '.($genericDocumentType?'present':'missing')."\n";
    if ($missing) {
        fwrite(STDERR, 'FAIL missing tables: '.implode(', ', $missing)."\n");
        exit(1);
    }
    if ($counts['roles'] < 4 || $counts['permissions'] < 22 || $counts['provinces'] < 9 || $counts['membership_types'] < 3 || $registeredRoleCount!==1 || $membershipColumnCount!==1 || $missingProfileColumns || !$profileUserUnique || $missingApplicationColumns || $missingEventColumns || !$applicationNumberUnique || $missingDocumentColumns || !$documentReferenceUnique || !$genericDocumentType) {
        fwrite(STDERR, "FAIL reference-data baseline is incomplete\n");
        exit(1);
    }
    echo "PASS schema and reference-data baseline\n";
} catch (Throwable $exception) {
    fwrite(STDERR, 'FAIL schema verification ('.get_class($exception).")\n");
    exit(1);
}
