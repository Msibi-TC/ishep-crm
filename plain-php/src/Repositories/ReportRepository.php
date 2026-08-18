<?php
namespace Ishep\Repositories;

use PDO;

final class ReportRepository
{
    public function __construct(private PDO $db) {}

    public function dashboard(int $year): array
    {
        $year = max(2000, min(2100, $year));
        return [
            'member_totals' => $this->memberTotals(),
            'members_by_type' => $this->membersBy('mt.name', 'membership_type'),
            'members_by_province' => $this->membersBy('p.name', 'province'),
            'members_by_profession' => $this->membersBy('pr.name', 'profession'),
            'account_statuses' => $this->accountStatuses(),
            'revenue_by_year' => $this->cashByYear(),
            'cash_by_type' => $this->cashBy('mt.name', 'membership_type'),
            'cash_by_province' => $this->cashBy('p.name', 'province'),
            'cash_by_profession' => $this->cashBy('pr.name', 'profession'),
            'membership_trend' => $this->membershipTrend($year),
        ];
    }

    private function memberTotals(): array
    {
        return $this->db->query("SELECT COUNT(*) total_members, SUM(status='active') active_members, SUM(status='suspended') suspended_members, SUM(status='expired') expired_members FROM memberships")->fetch() ?: [];
    }

    private function membersBy(string $group, string $key): array
    {
        $statement = $this->db->query("SELECT {$group} label, COUNT(DISTINCT m.id) total FROM memberships m JOIN membership_types mt ON mt.id=m.membership_type_id JOIN users u ON u.id=m.user_id LEFT JOIN member_profiles mp ON mp.user_id=u.id LEFT JOIN provinces p ON p.id=mp.province_id LEFT JOIN professions pr ON pr.id=mp.profession_id GROUP BY {$group} ORDER BY total DESC, label");
        return [$key => $statement->fetchAll()];
    }

    private function accountStatuses(): array
    {
        $statement = $this->db->query("SELECT account_status label, COUNT(*) total FROM users GROUP BY account_status ORDER BY label");
        return $statement->fetchAll();
    }

    private function cashByYear(): array
    {
        return $this->db->query("SELECT YEAR(p.received_at) label, SUM(p.amount) total FROM payments p WHERE p.status='completed' GROUP BY YEAR(p.received_at) ORDER BY label DESC")->fetchAll();
    }

    private function cashBy(string $group, string $key): array
    {
        $group = str_replace('p.name', 'prov.name', $group);
        $statement = $this->db->query("SELECT {$group} label, COALESCE(SUM(pa.amount),0) total FROM payment_allocations pa JOIN payments pay ON pay.id=pa.payment_id AND pay.status='completed' JOIN invoices i ON i.id=pa.invoice_id JOIN memberships m ON m.id=i.membership_id JOIN membership_types mt ON mt.id=m.membership_type_id JOIN users u ON u.id=m.user_id LEFT JOIN member_profiles mp ON mp.user_id=u.id LEFT JOIN provinces prov ON prov.id=mp.province_id LEFT JOIN professions pr ON pr.id=mp.profession_id GROUP BY {$group} ORDER BY total DESC, label");
        return [$key => $statement->fetchAll()];
    }

    private function membershipTrend(int $year): array
    {
        $from = $year - 4;
        $statement = $this->db->prepare("SELECT years.year label, COUNT(m.id) total FROM (SELECT ? year UNION ALL SELECT ? UNION ALL SELECT ? UNION ALL SELECT ? UNION ALL SELECT ?) years LEFT JOIN memberships m ON YEAR(m.created_at)=years.year GROUP BY years.year ORDER BY years.year");
        $statement->execute([$from, $from + 1, $from + 2, $from + 3, $year]);
        return $statement->fetchAll();
    }
}