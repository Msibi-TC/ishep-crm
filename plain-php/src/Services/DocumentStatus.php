<?php
namespace Ishep\Services;use DomainException;
final class DocumentStatus{public const ALL=['pending','verified','rejected','replaced','removed'];private const MAP=['pending'=>['verified','rejected','replaced','removed'],'rejected'=>['replaced','removed'],'verified'=>[],'replaced'=>[],'removed'=>[]];public function assert(string $from,string $to):void{if(!in_array($to,self::MAP[$from]??[],true))throw new DomainException("Invalid document status transition: $from to $to.");}public function label(string $s):string{return ucfirst($s);}}
