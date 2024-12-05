<?php

namespace App\Importing;

use App\Models\Crate;
use DateTimeHelper;
use DB;

class ImportCrates
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
        $crates = DB::connection('mysql_old')->select('select * from crate');

        $mappedArray            = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;

        foreach ($crates as $crate) {
            $iata = null;
            if (strpos($crate->iata, '-')) {
                $iata = explode('-', $crate->iata);
            }

            array_push($mappedArray, [
                'old_id'          => $crate->idcrate,
                'name'            => $crate->name,
                'iata_code'       => (!is_null($iata)) ? $iata[0] : $crate->iata,
                'type'            => $crate->crate_type,
                'animal_quantity' => $crate->number,
                'length'          => $crate->l,
                'wide'            => $crate->w,
                'height'          => $crate->h,
                'weight'          => $crate->weight,
                'currency'        => $crate->currency,
                'cost_price'      => $crate->cprice,
                'sale_price'      => $crate->price,
                'updated_at'      => ($crate->datemodified_crate != null && DateTimeHelper::validateDate($crate->datemodified_crate)) ? date('Y-m-d H:i:s', strtotime($crate->datemodified_crate)) : null,
            ]);

            $count_imported_records++;
        }

        Crate::insert($mappedArray);

        return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
    }
}
