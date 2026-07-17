<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $users;

    public function __construct($users = null)
    {
        $this->users = $users;
    }

    public function collection()
    {
        if ($this->users) {
            return $this->users;
        }
        return User::all();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Full Name',
            'Email',
            'Mobile Number',
            'Clinic Name',
            'Registration Number',
            'City',
            'State',
            'Status',
            'Registered On',
            'Last Seen'
        ];
    }

    public function map($user): array
    {
        $onlineThreshold = \Carbon\Carbon::now()->subMinutes(5);
        $isOnline = $user->last_seen_at && $user->last_seen_at >= $onlineThreshold;

        return [
            $user->id,
            $user->full_name,
            $user->email_id,
            $user->mobile_number,
            $user->clinic_name ?? '-',
            $user->registration_number ?? '-',
            $user->city ?? '-',
            $user->state ?? '-',
            $isOnline ? 'Online' : 'Offline',
            $user->created_at ? $user->created_at->format('d M Y H:i') : '-',
            $user->last_seen_at ? $user->last_seen_at->format('d M Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}