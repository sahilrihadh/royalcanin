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

class PreviousSessionsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithEvents
{
    protected $sessions;
    protected $sessionName;

    public function __construct($sessions, $sessionName = null)
    {
        $this->sessions = $sessions;
        $this->sessionName = $sessionName;
    }

    public function collection()
    {
        return $this->sessions;
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Email',
            'Webinar',
            'Watched On',
            'Certificate Status'
        ];
    }

    public function map($session): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $session->name,
            $session->email_id,
            ucfirst($session->session_name),
            $session->watched_on ? $session->watched_on->format('d M Y H:i') : 'N/A',
            $session->certificate_status == 1 ? 'Sent' : 'Pending'
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

                $sheet->getStyle('A1:F1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('2C3E50');

                $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('C')->setWidth(30);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(16);

                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A1:F' . $lastRow)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                if ($this->sessionName) {
                    $sheet->setCellValue('H1', 'Webinar:');
                    $sheet->setCellValue('I1', ucfirst($this->sessionName));
                    $sheet->getStyle('H1:I1')->getFont()->setBold(true);
                }
            },
        ];
    }
}