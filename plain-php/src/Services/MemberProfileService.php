<?php
namespace Ishep\Services;
use PDO;
use Throwable;
use Ishep\Repositories\UserRepository;
use Ishep\Repositories\MemberProfileRepository;
use Ishep\Repositories\AuditLogRepository;

final class MemberProfileService
{
    public function __construct(private PDO $db, private UserRepository $users, private MemberProfileRepository $profiles, private AuditLogRepository $audits) {}
    public function update(int $userId, array $data, string $ip): int
    {
        $this->db->beginTransaction();
        try {
            $this->users->updateProfileFields($userId, $data['name'], $data['membership_type_id']);
            $id = $this->profiles->save($userId, $data);
            $this->audits->record($userId, 'member_profile.updated', 'member_profile', $id, $ip);
            $this->db->commit();
            return $id;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $exception;
        }
    }
}
