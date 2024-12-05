<?php

namespace App\Importing;

use App\Models\BankAccount;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Str;

class ImportBankAccounts
{
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $accounts = DB::connection('mysql_old')->select('select * from bank_account');

        $mappedArray            = [];
        $count_imported_records = 0;
        $count_skipped_records  = 0;
        $now                    = Carbon::now();

        foreach ($accounts as $account) {
            $softdelete = null;

            if (Str::startsWith($account->shortname, 'FBME')) {
                $softdelete = $now;
            }

            if (Str::startsWith($account->shortname, 'EPB')) {
                $softdelete = $now;
            }

            if (Str::startsWith($account->shortname, 'Baltikums Bank')) {
                $softdelete = $now;
            }

            array_push($mappedArray, [
                'name'                                 => $account->shortname,
                'iban'                                 => $account->ibancode,
                'currency'                             => $account->acc_currency,
                'company_name'                         => $account->acc_company,
                'company_address'                      => $account->beneficiary_address,
                'beneficiary_name'                     => $account->beneficiary_bank,
                'beneficiary_fullname'                 => $account->beneficiary_bank . '-' . $account->acc_currency,
                'beneficiary_address'                  => $account->beneficiary_bank_address,
                'beneficiary_account'                  => $account->beneficiary_acc_number,
                'beneficiary_swift'                    => $account->beneficiary_bank_swiftcode,
                'beneficiary_aba'                      => $account->abba ?: null,
                'correspondent_name'                   => $account->correspondent_bank ?: null,
                'correspondent_address'                => $account->correspondent_bank_address ?: null,
                'correspondent_swift'                  => $account->correspondant_bank_swiftcode ?: null,
                'beneficiary_account_in_correspondent' => $account->beneficiary_bank_account_in_correspondant_bank ?: null,
                'created_at'                           => $now,
                'deleted_at'                           => $softdelete,
            ]);

            $count_imported_records++;
        }

        BankAccount::insert($mappedArray);

        return ['count_imported_records' => $count_imported_records, 'count_skipped_records' => $count_skipped_records];
    }
}
