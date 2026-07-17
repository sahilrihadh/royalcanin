<?php

namespace App\Exports;

use App\Models\Question;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class QuestionsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $questions;

    public function __construct($questions = null)
    {
        $this->questions = $questions;
    }

    public function collection()
    {
        if ($this->questions) {
            return $this->questions;
        }
        return Question::with('user')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'User Name',
            'User Email',
            'Question',
            'Answer',
            'Status',
            'Submitted Date',
            'Answered Date'
        ];
    }

    public function map($question): array
    {
        return [
            $question->id,
            $question->user->full_name ?? $question->user->name ?? 'N/A',
            $question->user->email_id ?? $question->user->email ?? 'N/A',
            $question->question_text ?? $question->question_input ?? '',
            $question->answer_text ?? 'Not answered yet',
            $question->is_answered ? 'Answered' : 'Pending',
            $question->created_at ? $question->created_at->format('d M Y h:i A') : '',
            $question->answered_at ? $question->answered_at->format('d M Y h:i A') : 'Not answered',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}