<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit(1);}
require __DIR__.'/_bootstrap.php';
use Ishep\Repositories\AuditLogRepository;
use Ishep\Security\PasswordPolicy;
use Ishep\Services\UserAdministrationService;
$email=null;foreach(array_slice($argv,1) as $arg)if(str_starts_with($arg,'--email='))$email=substr($arg,8);
try{if(!$email)throw new DomainException('Missing --email.');$password=rtrim((string)stream_get_contents(STDIN),"\r\n");$service=new UserAdministrationService(cli_db(),new AuditLogRepository(cli_db()));$service->resetStaffPassword($email,$password);fwrite(STDOUT,"Temporary password reset successfully. Store it securely; it was not logged.\n");}catch(Throwable $e){fwrite(STDERR,'ERROR: '.$e->getMessage().PHP_EOL);exit(1);}