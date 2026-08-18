<?php
namespace Ishep\Repositories;
use PDO;
final class ReferenceRepository
{
    public function __construct(private PDO $db) {}
    public function provinces():array{return$this->active('provinces');}
    public function professions():array{return$this->active('professions');}
    public function valid(string $table,int $id):bool{if(!in_array($table,['provinces','professions'],true))return false;$s=$this->db->prepare("SELECT COUNT(*) FROM `$table` WHERE id=? AND active=1");$s->execute([$id]);return(int)$s->fetchColumn()===1;}
    private function active(string $table):array{$s=$this->db->prepare("SELECT id,name FROM `$table` WHERE active=1 ORDER BY name");$s->execute();return$s->fetchAll();}
}
