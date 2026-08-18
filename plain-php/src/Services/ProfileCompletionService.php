<?php
namespace Ishep\Services;
final class ProfileCompletionService
{
    private const REQUIRED=['name','email','membership_type_id','telephone','province_id','profession_id'];
    public function calculate(array $profile):array{$complete=0;$missing=[];foreach(self::REQUIRED as$field){if(isset($profile[$field])&&trim((string)$profile[$field])!=='')$complete++;else$missing[]=$field;}return['percent'=>(int)round($complete/count(self::REQUIRED)*100),'missing'=>$missing,'complete'=>$complete===count(self::REQUIRED)];}
}
