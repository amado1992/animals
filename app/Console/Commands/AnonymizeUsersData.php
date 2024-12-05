<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AnonymizeUsersData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zoo:anonymize-users';

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

        $total_users = User::whereNotNull('email')->where('email', 'not like', 'a.%')->get();
        $this->info($total_users->count() . ' users to be anonimized');
        foreach ($total_users as $user) {
            if ($user->name) {
                $user->name = $faker->firstName;
            }

            if ($user->last_name) {
                $user->last_name = $faker->lastName;
            }

            if ($user->email) {
                $user->email = 'a.' . Str::camel(Str::random(12)) . $faker->unique()->safeEmail;
            }

            if ($user->password) {
                $user->password = $faker->password;
            }

            $user->save();
        }
    }
}
