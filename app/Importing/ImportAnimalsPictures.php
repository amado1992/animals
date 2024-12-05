<?php

namespace App\Importing;

use App\Models\Animal;
use Illuminate\Support\Facades\Storage;

class ImportAnimalsPictures
{
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $animals = Animal::get();

        $count_imported_records = 0;
        $count_skipped_records  = 0;
        $skipped_records        = collect();

        try {
            foreach ($animals as $animal) {
                $files = Storage::allFiles('public/animals_pictures_old/' . $animal->old_id);

                // animal has no pictures
                if (!$files || count($files) == 0) {
                    continue;
                }

                foreach ($files as $file) {
                    $file = pathinfo($file);
                    if (Storage::exists('public/animals_pictures_old/' . $animal->old_id . '/' . $file['basename']) && !Storage::exists('public/animals_pictures/' . $animal->id . '/' . $file['basename'])) {
                        Storage::copy('public/animals_pictures_old/' . $animal->old_id . '/' . $file['basename'], 'public/animals_pictures/' . $animal->id . '/' . $file['basename']);

                        if (strpos($file['basename'], 'newWebsite') !== false) {
                            $animal->update(['catalog_pic' => $file['basename']]);
                        }

                        $count_imported_records++;
                    }
                }
            }

            if (count($skipped_records) > 0) {
                \Storage::disk('local')->put('import_skip_animals_pictures', $skipped_records->toArray());
            }

            return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
        } catch (\Throwable $th) {
            echo 'Rolled back import animal pictures after ' . $count_imported_records . ' succesfull records and skipped ' . $count_skipped_records . ' records';
            throw $th;
        }
    }
}
