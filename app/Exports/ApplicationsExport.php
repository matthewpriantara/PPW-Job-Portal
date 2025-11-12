<?php

namespace App\Exports;

use App\Models\Application;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;

class ApplicationsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $jobId;

    public function __construct($jobId = null)
    {
        $this->jobId = $jobId;
    }

    /**
     * Return a collection of applications to export
     *
     * @return \Illuminate\Support\Collection
     */
public function collection()
{
    $query = Application::with(['user', 'jobVacancy.job']);

    if ($this->jobId) {
        $query->where('job_id', $this->jobId);
    }

    return $query->get();
}

    /**
     * Map a single application model to an array for export
     *
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            optional($row->user)->name,
            optional($row->jobVacancy->job)->title,
            optional($row->jobVacancy)->position,
            $row->status,
            $row->created_at ? $row->created_at->format('Y-m-d H:i:s') : null,
        ];
    }

    /**
     * Headings for the exported file
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Applicant',
            'Job Title',
            'Position',
            'Status',
            'Applied At',
        ];
    }
}
