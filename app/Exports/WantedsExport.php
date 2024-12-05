<?php

namespace App\Exports;

use App\Models\Wanted;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class WantedsExport implements FromView, WithProperties, WithStyles, ShouldAutoSize //, WithColumnWidths
{
    protected $wanteds;

    public function __construct($wanteds)
    {
        $this->wanteds = $wanteds;
    }

    public function properties(): array
    {
        return [
            'creator'     => 'International Zoo Services',
            'title'       => 'Wanteds Export',
            'description' => 'Wanteds details',
            'subject'     => 'Wanteds',
            'keywords'    => 'Wanteds,export,spreadsheet',
            'category'    => 'Wanteds',
            'manager'     => 'IZS',
            'company'     => 'IZS',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A2:G2000')->getFont()->setName('Arial');
        $sheet->getStyle('A2:G2000')->getFont()->setSize(9);

        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A2:G2000')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        /*return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]]
        ];*/
    }

    /*public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'c' => 25,
            'D' => 20,
            'E' => 25,
            'F' => 20,
            'G' => 20,
            'H' => 20
        ];
    }*/

    public function view(): View
    {
        return view('wanted.export_template', [
            'wanteds' => $this->wanteds,
        ]);
    }
}
