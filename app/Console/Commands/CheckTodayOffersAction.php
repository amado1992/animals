<?php

namespace App\Console\Commands;

use App\Mail\SendGeneralEmail;
use App\Models\Offer as OfferController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class CheckTodayOffersAction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:offers-today-action';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily new offers action to John.';

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
        $offerActions = OfferController::getTodayOfferAction();

        if (count($offerActions) > 0) {
            $email_from    = 'info@zoo-services.com';
            $email_to      = 'johnrens@zoo-services.com';
            $email_subject = 'Please check the offers actions to remind.';
            $email_body    = view('emails.offers-to-remind-admin', compact('offersToRemind'))->render();

            Mail::to($email_to)->send(new SendGeneralEmail($email_from, $email_subject, $email_body));

            $this->info('Successfully sent offers action to remind to John.');
        } else {
            $this->info('There are not offers action to remind.');
        }
    }
}
