<?php

namespace App\Console\Commands;

use App\Models\OurSurplus;
use Carbon\Carbon;
use DOMPDF;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GenerateInventoryListPdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:inventory-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate inventory list pdf';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $surplusToPrint = OurSurplus::where('is_public', true)->get()->groupBy(['animal.classification.class.common_name', 'animal.classification.order.common_name']);

            $document = 'pdf';

            $date = Carbon::now()->format('F j, Y');

            $templateName = 'surplus_template';
            $templateName .= '_no_prices';
            $templateName .= '_no_pictures';
            $is_standard = true;

            /**********************************************************************/

            $language        = 'english';
            $fileEnglishName = 'Inventory_list.pdf';
            $header_tittle   = 'Inventory list';

            $content = view('pdf_documents.' . $templateName, compact('header_tittle', 'date', 'document', 'language', 'surplusToPrint', 'is_standard'))->render();
            $html    = str_replace('http://localhost', base_path() . '/public', $content);

            $document = DOMPDF::loadHtml($html)->setPaper('a4', 'portrait');

            Storage::put('public/inventory/' . $fileEnglishName, $document->output());

            /**********************************************************************/

            $language        = 'spanish';
            $fileSpanishName = 'Lista_inventario.pdf';
            $header_tittle   = 'Lista de excedentes';

            $content = view('pdf_documents.' . $templateName, compact('header_tittle', 'date', 'document', 'language', 'surplusToPrint', 'is_standard'))->render();
            $html    = str_replace('http://localhost', base_path() . '/public', $content);

            $document = DOMPDF::loadHtml($html)->setPaper('a4', 'portrait');

            Storage::put('public/inventory/' . $fileSpanishName, $document->output());

            /**********************************************************************/

            $this->info('Inventory list generated successfully.');
        } catch (\Throwable $th) {
            $this->info('Error generating the file.');
        }
    }
}
