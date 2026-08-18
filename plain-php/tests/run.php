<?php
declare(strict_types=1);
$base=dirname(__DIR__);$autoload=$base.'/vendor/autoload.php';if(is_file($autoload))require$autoload;else{spl_autoload_register(static function(string $class)use($base):void{$p='Ishep\\';if(str_starts_with($class,$p)){$f=$base.'/src/'.str_replace('\\','/',substr($class,strlen($p))).'.php';if(is_file($f))require$f;}});require$base.'/src/Support/helpers.php';}
use Ishep\Http\{Request,Response};use Ishep\Routing\Router;use Ishep\Validation\Validator;
$tests=0;$failures=[];$check=function(bool $ok,string $name)use(&$tests,&$failures){$tests++;if(!$ok)$failures[]=$name;};
$check(e('<script>')==='&lt;script&gt;','HTML escaping');
$v=(new Validator())->validate(['email'=>'bad','password'=>'short','password_confirmation'=>'other','terms'=>'0'],['email'=>['email'],'password'=>['min:8','confirmed'],'terms'=>['accepted']]);$check(isset($v['email'],$v['password'],$v['terms']),'validation rules');
$router=new Router();$router->get('/hello/{name}',fn($r)=>new Response('Hello '.$r->get('name')));$response=$router->dispatch(new Request('GET','/hello/Ada',[],[],[]));$check($response->status===200&&$response->body==='Hello Ada','route parameters');$check($router->dispatch(new Request('POST','/hello/Ada',[],[],[]))->status===405,'405 handling');$check($router->dispatch(new Request('GET','/missing',[],[],[]))->status===404,'404 handling');
$required=['/','/membership','/careers','/bursaries','/verify-membership','/health','/register','/login','/logout','/forgot-password','/reset-password/{token}','/reset-password','/dashboard','/dashboard/administrator','/dashboard/finance','/dashboard/super-user'];$index=(string)file_get_contents($base.'/public/index.php');foreach($required as$route)$check(str_contains($index,"'".$route."'"),'route '.$route);
$example=(string)file_get_contents($base.'/.env.example');$sql=(string)file_get_contents($base.'/database/install.sql');$sqlStatements=(string)preg_replace('/--.*$/m','',$sql);
$check(str_contains($example,'DB_DATABASE=ishep_crm'),'example database name');
$check((bool)preg_match('/\bUSE\s+`ishep_crm`\s*;/i',$sql),'installer target database');
$legacyDatabase='ishep_crm'.'_personal';$check(!str_contains(strtolower($sql),$legacyDatabase),'legacy database name absent');
$check(!preg_match('/\b(DROP\s+DATABASE|CREATE\s+DATABASE|DROP\s+TABLE|TRUNCATE\s+(?:TABLE\s+)?)/i',$sqlStatements),'installer has no destructive database statements');
if($failures){fwrite(STDERR,"FAIL (".count($failures)."/{$tests})\n - ".implode("\n - ",$failures)."\n");exit(1);}echo"PASS {$tests} checks\n";
