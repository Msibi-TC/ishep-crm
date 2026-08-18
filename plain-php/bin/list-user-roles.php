<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit(1);}
require __DIR__.'/_bootstrap.php';
use Ishep\Repositories\AuditLogRepository;
use Ishep\Services\UserAdministrationService;
$email=null;foreach(array_slice($argv,1) as $arg)if(str_starts_with($arg,'--email='))$email=substr($arg,8);
try{$rows=(new UserAdministrationService(cli_db(),new AuditLogRepository(cli_db())))->listUsers($email);foreach($rows as $row)fwrite(STDOUT,sprintf("%s | %s | %s | membership: %s\n",$row['email'],$row['account_status'],$row['roles']?:'none',$row['membership_type_name']?:'none (staff/unassigned)'));}catch(Throwable $e){fwrite(STDERR,'ERROR: '.$e->getMessage().PHP_EOL);exit(1);}