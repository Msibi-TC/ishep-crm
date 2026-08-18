<?php
namespace Ishep\Http\Controllers;
use Throwable;use DomainException;use Ishep\Bootstrap\Application as App;use Ishep\Http\{Request,Response};
final class MembershipApplicationController
{
 private function app():App{return App::instance();}private function uid():int{return(int)$this->app()->session()->get('user_id');}
 private function data():array{$profile=$this->app()->profiles()->byUserId($this->uid())??[];$a=$this->app()->applications()->latestForUser($this->uid());return['application'=>$a,'events'=>$a?$this->app()->applications()->events((int)$a['id']):[],'profile'=>$profile,'completion'=>$this->app()->profileCompletion()->calculate($profile),'membershipTypes'=>$this->app()->memberships()->active(),'statuses'=>$this->app()->applicationStatuses()];}
 public function show():Response{return$this->app()->render('membership/application',$this->data());}public function create():Response{return$this->app()->render('membership/create',$this->data());}
 private function fail(Throwable $e,string $path):Response{$message=$e instanceof DomainException?$e->getMessage():'The application could not be updated safely.';if(!$e instanceof DomainException)$this->app()->logger()->log('error','Membership application request failed',['exception'=>get_class($e),'user_id'=>$this->uid()]);$this->app()->session()->flash('errors',['application'=>[$message]]);return Response::redirect(url($path));}
 public function save(Request $r):Response{try{$type=ctype_digit((string)($r->input['membership_type_id']??''))?(int)$r->input['membership_type_id']:0;$this->app()->applicationService()->saveDraft($this->uid(),$type,$r->ip());$this->app()->session()->flash('status','Application draft saved.');return Response::redirect(url('/membership/application'));}catch(Throwable $e){return$this->fail($e,'/membership/application/create');}}
 public function submit(Request $r):Response{try{$this->app()->applicationService()->submit($this->uid(),($r->input['declaration']??'')==='1',$r->ip());$this->app()->session()->flash('status','Your application has been submitted.');return Response::redirect(url('/membership/application'));}catch(Throwable $e){return$this->fail($e,'/membership/application');}}
 public function withdraw(Request $r):Response{try{$this->app()->applicationService()->withdraw($this->uid(),$r->ip());$this->app()->session()->flash('status','Your application has been withdrawn.');return Response::redirect(url('/membership/application'));}catch(Throwable $e){return$this->fail($e,'/membership/application');}}
}
