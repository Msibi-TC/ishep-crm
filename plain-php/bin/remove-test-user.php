<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit(1);}
require __DIR__.'/_bootstrap.php';
use Ishep\Repositories\AuditLogRepository;
use Ishep\Services\UserAdministrationService;
$email=null;$apply=false;$confirm=false;foreach(array_slice($argv,1) as $arg){if(str_starts_with($arg,'--email='))$email=substr($arg,8);if($arg==='--apply')$apply=true;if($arg==='--confirm-non-test')$confirm=true;}
try{if(!$email)throw new DomainException('Missing --email.');$service=new UserAdministrationService(cli_db(),new AuditLogRepository(cli_db()));$row=$service->removeTestUser($email,$apply,$confirm);$action=$apply?'Removed':'Dry run; would remove';fwrite(STDOUT,sprintf("%s %s (%s; roles: %s; membership: %s).\n",$action,$row['email'],$row['account_status'],$row['roles']?:'none',$row['membership_type_name']?:'none'));if(!$apply)fwrite(STDOUT,"No write performed. Re-run with --apply only after review.\n");}catch(Throwable $e){fwrite(STDERR,'ERROR: '.$e->getMessage().PHP_EOL);exit(1);}