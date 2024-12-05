<?php

namespace App\Importing;

use App\Models\Country;
use App\Models\ZooAssociation;
use DB;

class ImportZooAssociations
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
        $countries = Country::pluck('id', 'name');

        $zooassociations = DB::connection('mysql_old')->select('select * from zooassociations');

        $count_imported_records = 0;
        $count_skipped_records  = 0;

        foreach ($zooassociations as $zooassociation) {
            $checked_by = '';
            if (!empty($zooassociation->checkBy)) {
                if (!empty($zooassociation->remark)) {
                    $checked_by = "\n\rChecked by: " . $zooassociation->checkBy;
                } else {
                    $checked_by = 'Checked by: ' . $zooassociation->checkBy;
                }
            }

            ZooAssociation::create([
                'area'         => (trim($zooassociation->continent)            != '') ? $zooassociation->continent : null,
                'country_id'   => ($zooassociation->country                    != '') ? $countries[$zooassociation->country] : null,
                'website'      => (trim($zooassociation->link)                 != '') ? $zooassociation->link : null,
                'name'         => (trim($zooassociation->association)          != '') ? $zooassociation->association : null,
                'remark'       => (trim($zooassociation->remark . $checked_by) != '') ? $zooassociation->remark . $checked_by : null,
                'status'       => (trim($zooassociation->status)               != '') ? $zooassociation->status : null,
                'started_date' => ($zooassociation->startedOn                  != '0000-00-00 00:00:00') ? $zooassociation->startedOn : null,
                'checked_date' => ($zooassociation->checkDate                  != '0000-00-00 00:00:00') ? $zooassociation->checkDate : null,
            ]);

            $count_imported_records++;
        }

        return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
    }
}
