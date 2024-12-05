<?php

namespace App\Importing;

use App\Models\InterestingWebsite;
use DB;
use Illuminate\Support\Facades\Crypt;

class ImportInterestingWebsites
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
        $interestingWebsites = DB::connection('mysql_old')->select('select * from interesting_websites');

        $mappedArray            = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;

        foreach ($interestingWebsites as $interestingWebsite) {
            array_push($mappedArray, [
                'siteName'      => $interestingWebsite->aName,
                'siteUrl'       => $interestingWebsite->aUrl,
                'siteRemarks'   => $interestingWebsite->aRemarks,
                'loginUsername' => $interestingWebsite->aLoginUsername,
                'loginPassword' => Crypt::encryptString($interestingWebsite->aLoginPassword),
                'siteCategory'  => $interestingWebsite->aCategory,
            ]);

            $count_imported_records++;
        }

        InterestingWebsite::insert($mappedArray);

        return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
    }
}
