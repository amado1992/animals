<?php

namespace App\Importing;

use App\Models\Animal;
use App\Models\Classification;
use DB;

class ImportClassificationCodes
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
        $animals = Animal::get();

        $count_imported_records = 0;
        $count_skipped_records  = 0;
        $skipped_records        = collect();

        try {
            DB::beginTransaction();

            foreach ($animals as $animal) {
                if ($animal->genus_id != null && $animal->code_number != null) {
                    $genus  = $animal->classification;
                    $family = $genus->above;
                    $order  = $family->above;
                    $class  = $order->above;

                    $animal_code_number_class  = substr($animal->code_number, 0, 2);
                    $animal_code_number_order  = substr($animal->code_number, 2, 2);
                    $animal_code_number_family = substr($animal->code_number, 4, 4);
                    $animal_code_number_genus  = substr($animal->code_number, 8, 4);

                    if ($class->code == null) {
                        $class->update(['code' => $animal_code_number_class]);
                    }

                    if ($order->code == null) {
                        $order->update(['code' => $animal_code_number_order]);
                    }

                    if ($family->code == null) {
                        $family->update(['code' => $animal_code_number_family]);
                    }

                    if ($genus->code == null) {
                        $genus->update(['code' => $animal_code_number_genus]);
                    }
                }

                $count_imported_records++;
            }

            DB::commit();

            $classificationsNull = Classification::whereNull('code')->get();
            foreach ($classificationsNull as $classification) {
                switch ($classification->rank) {
                    case 'class':
                    case 'order':
                        $classification->update(['code' => '00']);
                        break;
                    default:
                        $classification->update(['code' => '0000']);
                        break;
                }
            }

            if (count($skipped_records) > 0) {
                \Storage::disk('local')->put('import_skip_classification_codes', $skipped_records->toArray());
            }

            return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
        } catch (\Throwable $th) {
            DB::rollBack();

            echo 'Rolled back import classifications codes after ' . $count_imported_records . ' succesfull records and skipped ' . $count_skipped_records . ' records';

            throw $th;
        }
    }
}
