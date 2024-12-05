<?php

namespace App\Importing;

use App\Models\InterestingWebsite;
use DB;
use Illuminate\Support\Facades\Crypt;

class ImportCodes
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
        $codes = DB::connection('mysql_old')->select('select * from codes');

        $mappedArray            = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;

        foreach ($codes as $code) {
            array_push($mappedArray, [
                'siteName'      => $code->name,
                'siteCategory'  => 'other-credentials',
                'loginUsername' => $code->login,
                'loginPassword' => Crypt::encryptString($code->password),
                'only_for_john' => ($code->forJohn != null) ? $code->forJohn : 0,
            ]);

            $count_imported_records++;
        }

        InterestingWebsite::insert($mappedArray);

        return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
    }
}
