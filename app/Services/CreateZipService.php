<?php

namespace App\Services;

//use Http;
use App\Models\CurrencyRate;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use ZipArchive;

class CreateZipService
{
    public function createZip($data, $zipFileName)
    {
        if (!empty($data)) {
            $public_dir = public_path();
            $url_file   = $public_dir . '/storage/zip/' . $zipFileName;
            $zip        = new ZipArchive;
            if (!file_exists($public_dir . '/storage/zip')) {
                mkdir($public_dir . '/storage/zip', 0700, true);
            }
            if ($zip->open($url_file, ZipArchive::CREATE) === true) {
                $zip->addEmptyDir('invoices');
                foreach ($data as $row) {
                    $zip->addFile($row['url'], 'invoices/' . $row['name']);
                }
                $zip->close();
            }

            // Create Download Response
            if (file_exists($url_file)) {
                return $url_file;
            }
        }

        return '';
    }
}
