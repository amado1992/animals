<?php

namespace App\Exports;

use App\Models\OurWanted;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OurWantedsExport implements FromView, WithProperties, WithStyles, ShouldAutoSize //, WithColumnWidths
{
    protected $standard_wanteds;

    public function __construct($standard_wanteds)
    {
        $this->standard_wanteds = $standard_wanteds;
    }

    public function properties(): array
    {
        return [
            'creator'     => 'International Zoo Services',
            'title'       => 'Standard wanted Export',
            'description' => 'Standard wanted details',
            'subject'     => 'Standard wanted',
            'keywords'    => 'Wanted,export,spreadsheet',
            'category'    => 'Standard wanted',
            'manager'     => 'IZS',
            'company'     => 'IZS',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A2:F2000')->getFont()->setName('Arial');
        $sheet->getStyle('A2:F2000')->getFont()->setSize(9);

        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A2:F2000')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

        $sheet->getStyle('A1:F1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        /*return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]]
        ];*/
    }

    public function view(): View
    {
        return view('our_wanted.export_template', [
            'standard_wanteds' => $this->standard_wanteds,
        ]);
    }
}
