<?php

namespace App\Importing;

use App\Models\Classification;
use DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ImportClassificationFamilies
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
        $orders = Classification::where('rank', 'order')->pluck('id', 'scientific_name');

        $classifications = DB::connection('mysql_old')->select('SELECT DISTINCT familyscientificname, familycommonname, orderscientificname FROM animal WHERE orderscientificname <> ""');

        $mappedArray            = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;

        foreach ($classifications as $classification) {
            // Order has to be found
            if (!isset($orders[$classification->orderscientificname])) {
                exit;
            }

            $slug = Str::slug($classification->familycommonname, '_');

            $filtered = Arr::where($mappedArray, function ($value, $key) use ($slug) {
                return $value['common_name_slug'] == $slug;
            });

            array_push($mappedArray, [
                'common_name'      => $classification->familycommonname,
                'scientific_name'  => $classification->familyscientificname,
                'common_name_slug' => (count($filtered) > 0 || Classification::where('common_name_slug', $slug)->first() != null) ? null : $slug,
                'rank'             => 'family',
                'belongs_to'       => $orders[$classification->orderscientificname],
            ]);

            $count_imported_records++;
        }

        Classification::insert($mappedArray);

        return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
    }
}
