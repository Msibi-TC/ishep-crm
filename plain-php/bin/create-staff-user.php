<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit(1);}
require __DIR__.'/_bootstrap.php';
use Ishep\Repositories\AuditLogRepository;
use Ishep\Security\PasswordPolicy;
use Ishep\Services\UserAdministrationService;

$options=[];foreach(array_slice($argv,1) as $arg){if(str_starts_with($arg,'--')&&str_contains($arg,'=')){[$key,$value]=explode('=',substr($arg,2),2);$options[$key]=$value;}elseif(str_starts_with($arg,'--'))$options[substr($arg,2)]=true;}
$required=static function(string $key)use($options):string{$value=trim((string)($options[$key]??''));if($value==='')throw new DomainException('Missing --'.$key.'.');return$value;};
try{$name=$required('name');$email=$required('email');$role=$required('role');$password=bin2hex(random_bytes(12)).'Aa1';$service=new UserAdministrationService(cli_db(),new AuditLogRepository(cli_db()));if(isset($options['dry-run'])){$service->createStaff($name,$email,$role,$password,true);fwrite(STDOUT,"Dry run passed. No account was created.\n");exit(0);}$id=$service->createStaff($name,$email,$role,$password);fwrite(STDOUT,"Created disposable staff account ID {$id}.\nCopy this temporary password immediately; it is displayed exactly once: {$password}\n");}catch(Throwable $e){fwrite(STDERR,'ERROR: '.$e->getMessage().PHP_EOL);exit(1);}