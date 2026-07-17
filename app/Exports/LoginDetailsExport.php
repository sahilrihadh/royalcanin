<?php

namespace App\Exports;

use App\Models\LoginDetails;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LoginDetailsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $startDate;
    protected $endDate;
    protected $loginDetails;

    public function __construct($loginDetails, $startDate = null, $endDate = null)
    {
        $this->loginDetails = $loginDetails;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->loginDetails;
    }

    public function headings(): array
    {
        return [
            '#',
            'User Name',
            'Email',
            'Login Time',
            'Logout Time',
            'Duration (minutes)',
            'Status'
        ];
    }

    public function map($detail): array
    {
        static $rowNumber = 0;
        $rowNumber++;
        
        $duration = 0;
        if ($detail->login_time && $detail->logout_time) {
            $duration = $detail->login_time->diffInMinutes($detail->logout_time);
        } elseif ($detail->login_time && !$detail->logout_time) {
            $duration = $detail->login_time->diffInMinutes(now());
        }

        return [
            $rowNumber,
            $detail->user->full_name ?? 'Unknown',
            $detail->user->email_id ?? 'Unknown',
            $detail->login_time ? $detail->login_time->format('d M Y H:i:s') : 'N/A',
            $detail->logout_time ? $detail->logout_time->format('d M Y H:i:s') : 'Active',
            number_format($duration, 0),
            $detail->logout_time ? 'Logged Out' : 'Active'
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
                $sheet->getStyle('A1:G1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('2C3E50');
                
                // Center align headers
                $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('C')->setWidth(30);
                $sheet->getColumnDimension('D')->setWidth(22);
                $sheet->getColumnDimension('E')->setWidth(22);
                $sheet->getColumnDimension('F')->setWidth(18);
                $sheet->getColumnDimension('G')->setWidth(15);
                
                // Add border to all cells
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A1:G' . $lastRow)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                
                // Add filter summary if date range provided
                if ($this->startDate && $this->endDate) {
                    $sheet->setCellValue('I1', 'Date Range:');
                    $sheet->setCellValue('J1', $this->startDate . ' to ' . $this->endDate);
                    $sheet->getStyle('I1:J1')->getFont()->setBold(true);
                }
            },
        ];
    }
}