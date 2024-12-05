<?php

namespace App\Importing;

use App\Models\Classification;
use DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ImportClassificationGenera
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
        $families = Classification::where('rank', 'family')->pluck('id', 'scientific_name');

        $classifications = DB::connection('mysql_old')->select('SELECT DISTINCT genusscientificname, genuscommonname, familyscientificname FROM animal WHERE familyscientificname <> ""');

        $mappedArray            = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;

        foreach ($classifications as $classification) {
            // Family has to be found
            if (!isset($families[$classification->familyscientificname])) {
                exit;
            }

            $slug = Str::slug($classification->genuscommonname, '_');

            $filtered = Arr::where($mappedArray, function ($value, $key) use ($slug) {
                return $value['common_name_slug'] == $slug;
            });

            array_push($mappedArray, [
                'common_name'      => $classification->genuscommonname,
                'scientific_name'  => $classification->genusscientificname,
                'common_name_slug' => (count($filtered) > 0 || Classification::where('common_name_slug', $slug)->first() != null) ? null : $slug,
                'rank'             => 'genus',
                'belongs_to'       => $families[$classification->familyscientificname],
            ]);

            $count_imported_records++;
        }

        Classification::insert($mappedArray);

        return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
    }
}
