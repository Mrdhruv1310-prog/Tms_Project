<?php

namespace App\Exports;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MyTasksSummaryExport implements FromQuery, WithHeadings
{
    public function query()
    {
        // Filter tasks by the authenticated user
        return Task::query()->where('user_id', Auth::user()->id);
    }

    public function headings(): array
    {
        return ['ID', 'Title', 'Status', 'Due Date'];
    }
}
