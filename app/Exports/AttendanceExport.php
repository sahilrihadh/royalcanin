<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $attendance;

    /**
     * @param \Illuminate\Support\Collection $attendance  Collection of grouped
     *        attendance rows (one per user per day), as produced by
     *        LoginDetailController::groupSessionsByUserAndDay().
     */
    public function __construct($attendance, $startDate = null, $endDate = null)
    {
        $this->attendance = $attendance;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->attendance;
    }

    public function headings(): array
    {
        return [
            '#',
            'User Name',
            'Email',
            'Date',
            'Sessions',
            'First Login',
            'Last Activity',
            'Total Duration (minutes)',
            'Status'
        ];
    }

    public function map($detail): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $detail->user->full_name ?? 'Unknown',
            $detail->user->email_id ?? 'Unknown',
            \Carbon\Carbon::parse($detail->date)->format('d M Y'),
            $detail->session_count,
            $detail->first_login ? $detail->first_login->format('h:i A') : 'N/A',
            $detail->last_activity ? $detail->last_activity->format('h:i A') : ($detail->is_active ? 'Still active' : 'N/A'),
            number_format($detail->total_minutes, 0),
            $detail->is_active ? 'Active' : 'Logged Out'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set header background color
                $sheet->getStyle('A1:I1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('2C3E50');

                // Center align headers
                $sheet->getStyle('A1:I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('C')->setWidth(30);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(12);
                $sheet->getColumnDimension('F')->setWidth(15);
                $sheet->getColumnDimension('G')->setWidth(15);
                $sheet->getColumnDimension('H')->setWidth(22);
                $sheet->getColumnDimension('I')->setWidth(15);

                // Add border to all cells
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A1:I' . $lastRow)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                // Add filter summary if date range provided
                if ($this->startDate && $this->endDate) {
                    $sheet->setCellValue('K1', 'Date Range:');
                    $sheet->setCellValue('L1', $this->startDate . ' to ' . $this->endDate);
                    $sheet->getStyle('K1:L1')->getFont()->setBold(true);
                }
            },
        ];
    }
}