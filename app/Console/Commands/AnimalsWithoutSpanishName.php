<?php

namespace App\Console\Commands;

use App\Mail\SendGeneralEmail;
use App\Models\Animal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class AnimalsWithoutSpanishName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:animal-without-spanish-name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the animals that were published and do not have spanish name.';

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
        $animals = Animal::whereHas('our_surpluses')->whereNull('spanish_name')->get();

        if (count($animals) > 0) {
            $email_from    = 'request@zoo-services.com';
            $email_to      = 'izs@zoo-services.com';
            $email_subject = 'Animals without spanish name.';
            $email_body    = view('emails.new-animals-without-spanish-name', compact('animals'))->render();

            Mail::to($email_to)->send(new SendGeneralEmail($email_from, $email_subject, $email_body));

            $this->info('Successfully sent animals without spanish name.');
        } else {
            $this->info('There are not animals without spanish name.');
        }
    }
}
