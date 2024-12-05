<?php

namespace App\Importing;

use App\Models\Crate;
use DB;

class ImportCratesPeterOld
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
            $price_eur = null;
            $price_usd = null;

            if ($crate->currency === 'EUR') {
                $price_eur = $crate->price;
            } elseif ($crate->currency === 'USD') {
                $price_usd = $crate->price;
            }

            array_push($mappedArray, [
                'old_id'          => $crate->idcrate,
                'name'            => $crate->name,
                'iata_code'       => $crate->iata,
                'type'            => $crate->crate_type,
                'animal_quantity' => $crate->number,
                'length'          => $crate->l,
                'width'           => $crate->w,
                'height'          => $crate->h,
                'weight'          => $crate->weight,
                'price_eur'       => $price_eur,
                'price_usd'       => $price_usd,
            ]);

            $count_imported_records++;
        }

        Crate::insert($mappedArray);

        return 'Finished import with ' . $count_imported_records . ' records, but skipped ' . $count_skipped_records . ' records';
    }
}
