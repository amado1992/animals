<?php

namespace App\Importing;

use App\Models\Surplus;
use Illuminate\Support\Facades\Storage;

class ImportSurplusPictures
{
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $surpluses = Surplus::get();

        $count_imported_records = 0;
        $count_skipped_records  = 0;
        $skipped_records        = collect();

        try {
            foreach ($surpluses as $surplus) {
                $files = Storage::allFiles('public/surplus_pictures_old/' . $surplus->old_id);

                // animal has no pictures
                if (!$files || count($files) == 0) {
                    continue;
                }

                foreach ($files as $file) {
                    $file = pathinfo($file);
                    if (Storage::exists('public/surplus_pictures_old/' . $surplus->old_id . '/' . $file['basename']) && !Storage::exists('public/surpluses_docs/' . $surplus->id . '/' . $file['basename'])) {
                        Storage::copy('public/surplus_pictures_old/' . $surplus->old_id . '/' . $file['basename'], 'public/surpluses_docs/' . $surplus->id . '/' . $file['basename']);

                        $count_imported_records++;
                    }
                }
            }

            if (count($skipped_records) > 0) {
                Storage::disk('local')->put('import_skip_surplus_pictures', $skipped_records->toArray());
            }

            return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
        } catch (\Throwable $th) {
            echo 'Rolled back import surpluses pictures after ' . $count_imported_records . ' succesfull records and skipped ' . $count_skipped_records . ' records';
            throw $th;
        }
    }
}
