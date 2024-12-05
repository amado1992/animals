<?php

namespace App\Exports;

use App\Models\Animal;
use App\Models\Organisation;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InstitutionsAddressListExport implements FromView, WithProperties, WithStyles, ShouldAutoSize
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
            'title'       => 'Institutions address list',
            'description' => 'Institutions addresses',
            'subject'     => 'Institutions',
            'keywords'    => 'Institutions,export,spreadsheet',
            'category'    => 'Institutions',
            'manager'     => 'IZS',
            'company'     => 'IZS',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function view(): View
    {
        return view('institutions.export_address_list_template', [
            'institutions' => $this->institutions,
        ]);
    }
}
