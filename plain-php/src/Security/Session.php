<?php
namespace Ishep\Security;
final class Session
{
    public function start(string $name,int $minutes,string $path):void { if(session_status()===PHP_SESSION_ACTIVE)return; $seconds=$minutes*60; ini_set('session.use_strict_mode','1'); ini_set('session.use_only_cookies','1'); ini_set('session.gc_maxlifetime',(string)$seconds); session_name($name); session_save_path($path); session_set_cookie_params(['lifetime'=>$seconds,'path'=>'/','secure'=>(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off'),'httponly'=>true,'samesite'=>'Lax']); session_start(); if(isset($_SESSION['_last_activity'])&&$_SESSION['_last_activity']<time()-$seconds){$_SESSION=[];session_regenerate_id(true);} $_SESSION['_last_activity']=time(); }
    public function get(string $key,mixed $default=null):mixed{return $_SESSION[$key]??$default;} public function put(string $key,mixed $value):void{$_SESSION[$key]=$value;} public function forget(string $key):void{unset($_SESSION[$key]);}
    public function regenerate():void{session_regenerate_id(true);} public function flash(string $key,mixed $value):void{$_SESSION['_flash'][$key]=$value;} public function ageFlash():void{$_SESSION['_now']=$_SESSION['_flash']??[];unset($_SESSION['_flash']);} public function flashed(string $key,mixed $default=null):mixed{return $_SESSION['_now'][$key]??$default;} public function errors():array{return(array)$this->flashed('errors',[]);} public function old():array{return(array)$this->flashed('old',[]);}
    public function invalidate():void{$_SESSION=[];if(ini_get('session.use_cookies')){$p=session_get_cookie_params();setcookie(session_name(),'',time()-42000,$p['path'],$p['domain'],$p['secure'],$p['httponly']);}session_destroy();}
}
