<?php
namespace Ishep\Http\Controllers;

use Ishep\Bootstrap\Application as App;
use Ishep\Http\{Request,Response};

final class ReportController
{
    private function app(): App { return App::instance(); }

    public function index(Request $request): Response
    {
        $year = ctype_digit((string)($request->query['year'] ?? '')) ? (int)$request->query['year'] : (int)gmdate('Y');
        $reports = $this->app()->reports()->dashboard($year);
        if (($request->query['format'] ?? '') === 'csv') return $this->csv($reports, $year);
        return $this->app()->render('admin/reports', ['reports' => $reports, 'year' => $year]);
    }

    private function csv(array $reports, int $year): Response
    {
        $lines = ['report,label,total'];
        foreach ($reports as $report => $rows) foreach ($rows as $row) {
            if (!is_array($row) || !array_key_exists('label', $row)) continue;
            $lines[] = implode(',', array_map(static fn(mixed $value): string => '"'.str_replace('"', '""', (string)$value).'"', [$report, $row['label'], $row['total'] ?? '']));
        }
        return new Response(implode("\r\n", $lines)."\r\n", 200, ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="ishep-report-'.$year.'.csv"', 'Cache-Control' => 'private, no-store']);
    }
}