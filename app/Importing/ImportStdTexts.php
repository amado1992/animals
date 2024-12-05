<?php

namespace App\Importing;

use App\Models\StdText;
use DB;

class ImportStdTexts
{
    /**
     * Create a new import instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $stdTexts = DB::connection('mysql_old')->select('select * from stdtexts');

        $mappedArray            = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;

        foreach ($stdTexts as $stdText) {
            array_push($mappedArray, [
                'code'         => $stdText->code,
                'category'     => $stdText->category,
                'name'         => $stdText->name,
                'remarks'      => $stdText->remarks,
                'english_text' => $stdText->englishText,
                'spanish_text' => $stdText->spanishText,
                'updated_at'   => $stdText->datemodified_stdtext,
            ]);

            $count_imported_records++;
        }

        StdText::insert($mappedArray);

        return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
    }
}
