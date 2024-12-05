<?php

namespace App\Console\Commands;

use App\Mail\SendGeneralEmail;
use App\Models\Offer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class OffersToRemindToAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:offers-to-remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send offers to remind to admin.';

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
        $date           = Carbon::now()->format('Y-m-d');
        $offersToRemind = Offer::whereNotNull('next_reminder_at')
            ->whereDate('next_reminder_at', '<=', $date)
            ->get();

        if (count($offersToRemind) > 0) {
            $email_from    = 'info@zoo-services.com';
            $email_to      = 'johnrens@zoo-services.com';
            $email_cc      = 'rossmery@zoo-services.com';
            $email_subject = 'Please check the offers to remind.';
            $email_body    = view('emails.offers-to-remind-admin', compact('offersToRemind'))->render();

            Mail::to($email_to)->cc($email_cc)->send(new SendGeneralEmail($email_from, $email_subject, $email_body));

            $this->info('Successfully sent offers to remind to admin.');
        } else {
            $this->info('There are not offers to remind.');
        }
    }
}
