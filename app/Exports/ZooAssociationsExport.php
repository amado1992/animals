<?php

namespace App\Exports;

use App\Models\ZooAssociation;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ZooAssociationsExport implements FromView, WithStyles, WithColumnWidths
{
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        /*return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]]
        ];*/
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'c' => 25,
            'D' => 20,
            'E' => 25,
            'F' => 20,
            'G' => 20,
            'H' => 20,
        ];
    }

    public function view(): View
    {
        return view('zoo_associations.export_template', [
            'zooAssociations' => ZooAssociation::all(),
        ]);
    }
}
