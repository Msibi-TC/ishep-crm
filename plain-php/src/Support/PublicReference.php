<?php
namespace Ishep\Support;
final class PublicReference{public static function make(string $prefix):string{return'ISH-'.$prefix.'-'.gmdate('Y').'-'.strtoupper(bin2hex(random_bytes(8)));}}
