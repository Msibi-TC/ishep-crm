<?php
namespace Ishep\Services;
use DomainException;
final class MembershipApplicationStatus
{
    public const ALL=['draft','submitted','under_review','approved','rejected','withdrawn'];
    private const TRANSITIONS=['draft'=>['submitted','withdrawn'],'submitted'=>['under_review','withdrawn'],'under_review'=>['approved','rejected'],'rejected'=>['draft'],'approved'=>[],'withdrawn'=>[]];
    public function can(string $from,string $to):bool{return in_array($to,self::TRANSITIONS[$from]??[],true);}
    public function assert(string $from,string $to):void{if(!$this->can($from,$to))throw new DomainException("Invalid application status transition: $from to $to.");}
    public function label(string $status):string{return ucwords(str_replace('_',' ',$status));}
}
