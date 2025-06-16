<?php

namespace App\Services\_Common\Traits;

use Illuminate\Support\Arr;

trait ReporterTrait
{
    private array $reports = [];

    public function addReport($report, string $channel = 'default'): void
    {
        if (! isset($this->reports[$channel])) {
            $this->reports[$channel] = [];
        }

        if (! is_array($report) or Arr::isAssoc($report)) {
            $report = [$report];
        }

        $this->reports[$channel] = array_merge($this->reports[$channel], $report);
    }

    public function getReports(string $channel = 'default'): array
    {
        return $this->reports[$channel] ?? [];
    }

    public function getReportsAndClear(string $channel = 'default'): array
    {
        if (Arr::has($this->reports, $channel)) {
            $reports = $this->reports[$channel];
            $this->reports[$channel] = [];

            return $reports;
        }

        return [];
    }

    public function getReportPrintable(string $channel = 'default'): mixed
    {
        return print_r($this->getReports($channel), true);
    }

    public function clearReport(string $channel = 'default'): void
    {
        if (Arr::has($this->reports, $channel)) {
            $this->reports[$channel] = [];
        }

    }

    public function hasReport(string $channel = 'default'): bool
    {
        return ! empty($this->reports[$channel]);
    }
}
