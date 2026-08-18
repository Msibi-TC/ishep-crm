<?php
namespace Ishep\Repositories;
use PDO;
final class AuditLogRepository { public function __construct(private PDO $db){} public function record(?int $actor,string $action,string $type,?int $id,string $ip):void{$s=$this->db->prepare('INSERT INTO audit_logs (actor_user_id,action,entity_type,entity_id,ip_address,created_at) VALUES (?,?,?,?,?,UTC_TIMESTAMP())');$s->execute([$actor,$action,$type,$id,$ip]);} }
