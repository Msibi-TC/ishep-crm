<?php
namespace Ishep\Repositories;
use PDO;
final class MemberProfileRepository
{
    public function __construct(private PDO $db) {}
    public function byUserId(int $userId):?array{$s=$this->db->prepare('SELECT mp.id,mp.user_id,mp.telephone,mp.province_id,mp.profession_id,mp.organisation,mp.job_title,mp.biography,mp.created_at AS profile_created_at,mp.updated_at AS profile_updated_at,u.name,u.email,u.membership_type_id,u.account_status,u.last_login_at,u.created_at AS joined_at,u.updated_at AS account_updated_at,mt.name membership_type_name,p.name province_name,pr.name profession_name FROM users u LEFT JOIN member_profiles mp ON mp.user_id=u.id LEFT JOIN membership_types mt ON mt.id=u.membership_type_id LEFT JOIN provinces p ON p.id=mp.province_id LEFT JOIN professions pr ON pr.id=mp.profession_id WHERE u.id=? LIMIT 1');$s->execute([$userId]);return$s->fetch()?:null;}
    public function save(int $userId,array $data):int{$s=$this->db->prepare('INSERT INTO member_profiles (user_id,telephone,province_id,profession_id,organisation,job_title,biography,created_at,updated_at) VALUES (?,?,?,?,?,?,?,UTC_TIMESTAMP(),UTC_TIMESTAMP()) ON DUPLICATE KEY UPDATE telephone=VALUES(telephone),province_id=VALUES(province_id),profession_id=VALUES(profession_id),organisation=VALUES(organisation),job_title=VALUES(job_title),biography=VALUES(biography),updated_at=UTC_TIMESTAMP()');$s->execute([$userId,$data['telephone'],$data['province_id'],$data['profession_id'],$data['organisation'],$data['job_title'],$data['biography']]);$profile=$this->byUserId($userId);return(int)($profile['id']??0);}
}
