<?php
namespace Ishep\Repositories;
use PDO;

final class MembershipTypeRepository
{
    public function __construct(private PDO $db) {}
    public function active(): array { $s=$this->db->prepare('SELECT id, code, name FROM membership_types WHERE active = 1 ORDER BY name');$s->execute();return$s->fetchAll(); }
    public function activeById(int $id): ?array { $s=$this->db->prepare('SELECT id, code, name FROM membership_types WHERE id = ? AND active = 1');$s->execute([$id]);return$s->fetch()?:null; }
}
