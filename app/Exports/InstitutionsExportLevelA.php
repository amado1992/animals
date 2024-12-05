<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InstitutionsExportLevelA implements FromView, WithProperties, WithStyles, ShouldAutoSize //, WithColumnWidths
{
    protected $institutions;

    public function __construct($institutions)
    {
        $this->institutions = $institutions;
    }

    public function properties(): array
    {
        return [
            'creator'     => 'International Zoo Services',
            'title'       => 'Mailing list of level A institutions',
            'description' => 'Mailing list of level A institutions',
            'subject'     => 'Mailing list of level A institutions',
            'keywords'    => 'Institution,export,spreadsheet,Level',
            'category'    => 'Institutions',
            'manager'     => 'IZS',
            'company'     => 'IZS',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->getStyle('A1:J1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function view(): View
    {
        return view('institutions.export_email_isntitutions_level_a_template', [
            'institutions' => $this->institutions,
        ]);
    }
}
