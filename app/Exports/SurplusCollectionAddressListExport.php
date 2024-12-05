<?php

namespace App\Exports;

use App\Models\Animal;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SurplusCollectionAddressListExport implements FromView, WithProperties, WithStyles, ShouldAutoSize
{
    protected $surpluses;

    public function __construct($surpluses)
    {
        $this->surpluses = $surpluses;
    }

    public function properties(): array
    {
        return [
            'creator'     => 'International Zoo Services',
            'title'       => 'Surplus Collections address list',
            'description' => 'Surplus Collections addresses',
            'subject'     => 'Surplus Collections',
            'keywords'    => 'Surplus Collections,export,spreadsheet',
            'category'    => 'Surplus Collections',
            'manager'     => 'IZS',
            'company'     => 'IZS',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:C1')->getFont()->setBold(true);
        $sheet->getStyle('A1:C1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    }

    public function view(): View
    {
        return view('surplus_collections.export_address_list_template', [
            'surpluses' => $this->surpluses,
        ]);
    }
}
