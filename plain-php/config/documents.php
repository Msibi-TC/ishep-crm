<?php
return ['root'=>env('DOCUMENT_PRIVATE_ROOT',dirname(__DIR__).'/storage/private/documents'),'max_bytes'=>5*1024*1024,'allowed'=>['pdf'=>['application/pdf'],'jpg'=>['image/jpeg'],'jpeg'=>['image/jpeg'],'png'=>['image/png']]];
