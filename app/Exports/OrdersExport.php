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

class OrdersExport implements FromView, WithProperties, WithStyles, ShouldAutoSize //, WithColumnWidths
{
    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function properties(): array
    {
        return [
            'creator'     => 'International Zoo Services',
            'title'       => 'Orders Export',
            'description' => 'Orders details',
            'subject'     => 'Orders',
            'keywords'    => 'Orders,export,spreadsheet',
            'category'    => 'Orders',
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
        return view('orders.export_template', [
            'orders' => $this->orders,
        ]);
    }
}
