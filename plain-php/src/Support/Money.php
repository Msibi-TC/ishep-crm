<?php
namespace Ishep\Support;
use DomainException;
final class Money
{
 public static function normalize(string $value):string{$value=trim($value);if(!preg_match('/^\d{1,10}(?:\.\d{1,2})?$/',$value))throw new DomainException('Enter a valid non-negative amount with no more than two decimal places.');[$whole,$fraction]=array_pad(explode('.',$value,2),2,'');$whole=ltrim($whole,'0');return($whole===''?'0':$whole).'.'.str_pad($fraction,2,'0');}
 public static function minor(string $value):int{$value=trim($value);if(!preg_match('/^-?\d+(?:\.\d{1,2})?$/',$value))throw new DomainException('Invalid stored money value.');$negative=str_starts_with($value,'-');$value=ltrim($value,'-');[$whole,$fraction]=array_pad(explode('.',$value,2),2,'');$minor=((int)$whole*100)+(int)str_pad($fraction,2,'0');return$negative?-$minor:$minor;}
 public static function decimal(int $minor):string{$sign=$minor<0?'-':'';$minor=abs($minor);return$sign.intdiv($minor,100).'.'.str_pad((string)($minor%100),2,'0',STR_PAD_LEFT);}
 public static function currency(string $value):string{$value=strtoupper(trim($value));if(!preg_match('/^[A-Z]{3}$/',$value))throw new DomainException('Select a valid three-letter currency.');return$value;}
}
