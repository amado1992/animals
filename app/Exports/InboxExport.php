<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithProperties;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InboxExport implements FromView, WithProperties, WithStyles, ShouldAutoSize //, WithColumnWidths
{
    protected $emails;

    public function __construct($emails)
    {
        $this->emails = $emails;
    }

    public function properties(): array
    {
        return [
            'creator'     => 'International Zoo Services',
            'title'       => 'Emails Export',
            'description' => 'Emails details',
            'subject'     => 'Emails',
            'keywords'    => 'Emails,export,spreadsheet',
            'category'    => 'Emails',
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
    }

    public function view(): View
    {
        return view('inbox.export_template', [
            'emails' => $this->emails,
        ]);
    }
}
