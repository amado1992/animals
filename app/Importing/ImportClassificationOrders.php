<?php

namespace App\Importing;

use App\Models\Classification;
use DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ImportClassificationOrders
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
        $classes = Classification::where('rank', 'class')->pluck('id', 'scientific_name');

        $classifications = DB::connection('mysql_old')->select('SELECT DISTINCT orderscientificname, ordercommonname, classscientificname FROM animal WHERE classscientificname <> ""');

        $mappedArray            = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;

        foreach ($classifications as $classification) {
            // Class has to be found
            if (!isset($classes[$classification->classscientificname])) {
                exit;
            }

            $slug = Str::slug($classification->ordercommonname, '_');

            $filtered = Arr::where($mappedArray, function ($value, $key) use ($slug) {
                return $value['common_name_slug'] == $slug;
            });

            array_push($mappedArray, [
                'common_name'      => $classification->ordercommonname,
                'scientific_name'  => $classification->orderscientificname,
                'common_name_slug' => (count($filtered) > 0 || Classification::where('common_name_slug', $slug)->first() != null) ? null : $slug,
                'rank'             => 'order',
                'belongs_to'       => $classes[$classification->classscientificname],
            ]);

            $count_imported_records++;
        }

        Classification::insert($mappedArray);

        return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
    }
}
