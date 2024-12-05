<?php

namespace App\Exports;

use App\Models\Airfreight;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AirfreightsExport implements FromView, WithProperties, WithStyles, ShouldAutoSize //, WithColumnWidths
{
    protected $airfreights;

    public function __construct($airfreights)
    {
        $this->airfreights = $airfreights;
    }

    public function properties(): array
    {
        return [
            'creator'     => 'International Zoo Services',
            'title'       => 'Airfreights Export',
            'description' => 'Airfreights details',
            'subject'     => 'Airfreights',
            'keywords'    => 'Airfreights,export,spreadsheet',
            'category'    => 'Airfreights',
            'manager'     => 'IZS',
            'company'     => 'IZS',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A2:O2000')->getFont()->setName('Arial');
        $sheet->getStyle('A2:O2000')->getFont()->setSize(9);

        $sheet->getStyle('A1:O1')->getFont()->setBold(true);
        $sheet->getStyle('A2:O2000')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

        $sheet->getStyle('A1:O1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

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
        return view('airfreights.export_template', [
            'airfreights' => $this->airfreights,
        ]);
    }
}
