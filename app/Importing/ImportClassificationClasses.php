<?php

namespace App\Importing;

use App\Models\Classification;
use DB;
use Illuminate\Support\Str;

class ImportClassificationClasses
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
        $classifications = DB::connection('mysql_old')->select('SELECT DISTINCT classscientificname, classcommonname FROM animal WHERE classscientificname <> ""');

        $mappedArray            = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;

        foreach ($classifications as $classification) {
            array_push($mappedArray, [
                'common_name'      => $classification->classcommonname,
                'scientific_name'  => $classification->classscientificname,
                'common_name_slug' => Str::slug($classification->classcommonname, '_'),
                'rank'             => 'class',
            ]);

            $count_imported_records++;
        }

        Classification::insert($mappedArray);

        return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
    }
}
