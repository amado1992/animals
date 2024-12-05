<?php

namespace App\Importing;

use App\Models\Guideline;
use DB;

class ImportGuidelines
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
        $guidelines = DB::connection('mysql_old')->select('select * from guidelines');

        $mappedArray            = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;

        foreach ($guidelines as $guideline) {
            array_push($mappedArray, [
                'subject'          => $guideline->subject,
                'remark'           => $guideline->guidelinetext,
                'category'         => $guideline->category,
                'related_filename' => $guideline->docName,
            ]);

            $count_imported_records++;
        }

        Guideline::insert($mappedArray);

        return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
    }
}
