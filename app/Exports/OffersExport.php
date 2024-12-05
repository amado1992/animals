<?php

namespace App\Exports;

use App\Models\Offer;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OffersExport implements FromView, WithProperties, WithStyles, ShouldAutoSize //, WithColumnWidths
{
    protected $offers;

    public function __construct($offers)
    {
        $this->offers = $offers;
    }

    public function properties(): array
    {
        return [
            'creator'     => 'International Zoo Services',
            'title'       => 'Offers Export',
            'description' => 'Offers details',
            'subject'     => 'Offers',
            'keywords'    => 'Offers,export,spreadsheet',
            'category'    => 'Offers',
            'manager'     => 'IZS',
            'company'     => 'IZS',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A2:H2000')->getFont()->setName('Arial');
        $sheet->getStyle('A2:H2000')->getFont()->setSize(9);

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H2000')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);

        $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

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
        return view('offers.export_template', [
            'offers' => $this->offers,
        ]);
    }
}
