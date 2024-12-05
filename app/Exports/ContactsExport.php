<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ContactsExport implements FromView, WithProperties, WithStyles, ShouldAutoSize //, WithColumnWidths
{
    protected $contacts;

    public function __construct($contacts)
    {
        $this->contacts = $contacts;
    }

    public function properties(): array
    {
        return [
            'creator'     => 'International Zoo Services',
            'title'       => 'Contact List',
            'description' => 'Contact details',
            'subject'     => 'Contacts',
            'keywords'    => 'Contact,export,spreadsheet',
            'category'    => 'Contacts',
            'manager'     => 'IZS',
            'company'     => 'IZS',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->getFont()->setBold(true);
        $sheet->getStyle('A1:K1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

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
        return view('contacts.export_template', [
            'contacts' => $this->contacts,
        ]);
    }
}
