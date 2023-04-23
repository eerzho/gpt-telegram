<?php

namespace App\Service;

use App\Entity\Report;
use App\Repository\ReportRepository;

readonly class ReportService
{
    public function __construct(private ReportRepository $reportRepository)
    {
    }

    public function save(Report $report): bool
    {
        return $this->reportRepository->save($report);
    }
}