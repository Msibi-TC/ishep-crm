<?php
namespace Ishep\Validation;

use Ishep\Repositories\MembershipTypeRepository;
use Ishep\Repositories\ReferenceRepository;

final class MemberProfileValidator
{
    public function __construct(private MembershipTypeRepository $memberships, private ReferenceRepository $references) {}

    public function validate(array $input): array
    {
        $data=[];
        foreach(['name','telephone','organisation','job_title','biography'] as $key)$data[$key]=trim((string)($input[$key]??''));
        foreach(['membership_type_id','province_id','profession_id'] as $key)$data[$key]=ctype_digit((string)($input[$key]??''))?(int)$input[$key]:0;
        $errors=(new Validator())->validate($data,[
            'name'=>['required','max:255'],'telephone'=>['required','max:30'],'membership_type_id'=>['required'],
            'province_id'=>['required'],'profession_id'=>['required'],'organisation'=>['max:255'],
            'job_title'=>['max:255'],'biography'=>['max:1000'],
        ]);
        if($data['telephone']!==''&&!preg_match('/^[0-9+() .-]{7,30}$/',$data['telephone']))$errors['telephone'][]='Enter a valid telephone number.';
        if(!$this->memberships->activeById($data['membership_type_id']))$errors['membership_type_id'][]='Select a valid active membership type.';
        foreach(['province'=>'province_id','profession'=>'profession_id'] as $reference=>$field)if(!$data[$field]||!$this->references->valid($reference.'s',$data[$field]))$errors[$field][]='Select a valid active '.$reference.'.';
        foreach(['organisation','job_title','biography'] as $key)$data[$key]=$data[$key]!==''?$data[$key]:null;
        return [$data,$errors];
    }
}
