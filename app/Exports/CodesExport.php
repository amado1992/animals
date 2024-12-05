<?php

namespace App\Exports;

use App\Models\Animal;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CodesExport implements FromView, WithProperties, WithStyles, ShouldAutoSize, WithColumnWidths
{
    protected $codes;

    public function __construct($codes)
    {
        $this->codes = $codes;
    }

    public function properties(): array
    {
        return [
            'creator'     => 'International Zoo Services',
            'title'       => 'Codes List',
            'description' => 'Codes details',
            'subject'     => 'Codes',
            'keywords'    => 'Codes,export,spreadsheet',
            'category'    => 'Codes',
            'manager'     => 'IZS',
            'company'     => 'IZS',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        /*return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]]
        ];*/
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 40,
            'C' => 40,
            'D' => 25,
            'E' => 25,
        ];
    }

    public function view(): View
    {
        return view('codes.export_template', [
            'codes' => $this->codes,
        ]);
    }
}
