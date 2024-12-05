<?php

namespace App\Console\Commands;

use App\Models\BankAccount;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AnonymizeBankAccountsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoo:anonymize-bank-accounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Anonymize data for testing purposes';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $faker = \Faker\Factory::create('nl_NL');

        $total_bank_accounts = BankAccount::get();
        $this->info($total_bank_accounts->count() . ' bank accounts to be anonimized');
        foreach ($total_bank_accounts as $bank_account) {
            if ($bank_account->iban) {
                $bank_account->iban = 'b.' . Str::camel(Str::random(12));
            }

            if ($bank_account->company_address) {
                $bank_account->company_address = $faker->streetAddress;
            }

            if ($bank_account->beneficiary_address) {
                $bank_account->beneficiary_address = $faker->streetAddress;
            }

            if ($bank_account->beneficiary_account) {
                $bank_account->beneficiary_account = Str::camel(Str::random(8));
            }

            if ($bank_account->beneficiary_swift) {
                $bank_account->beneficiary_swift = Str::camel(Str::random(8));
            }

            if ($bank_account->beneficiary_aba) {
                $bank_account->beneficiary_aba = Str::camel(Str::random(8));
            }

            if ($bank_account->correspondent_name) {
                $bank_account->correspondent_name = Str::camel(Str::random(10));
            }

            if ($bank_account->correspondent_address) {
                $bank_account->correspondent_address = Str::camel(Str::random(10));
            }

            if ($bank_account->correspondent_swift) {
                $bank_account->correspondent_swift = Str::camel(Str::random(8));
            }

            if ($bank_account->beneficiary_account_in_correspondent) {
                $bank_account->beneficiary_account_in_correspondent = Str::camel(Str::random(8));
            }

            $bank_account->save();
        }
    }
}
