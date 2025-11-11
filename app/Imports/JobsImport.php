<?php

namespace App\Imports;

use App\Models\Job;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class JobsImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Job([
            'title' => $row['title'],
            'description' => $row['description'],
            'department' => $row['department'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'department' => 'nullable|string|max:255',
        ];
    }
}
