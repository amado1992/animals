<?php

namespace App\Console\Commands;

use App\Mail\SendGeneralEmail;
use App\Models\Offer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class OffersToApproveToAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:offers-to-approve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send offers to approve to admin.';

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
        $offersToApprove = Offer::where('offer_status', 'Approval')->get();

        if (count($offersToApprove) > 0) {
            $email_from    = 'info@zoo-services.com';
            $email_to      = 'johnrens@zoo-services.com';
            $email_cc      = 'info@zoo-services.com';
            $email_subject = 'Please check the offers to approve.';
            $email_body    = view('emails.offers-to-approve-url-admin', compact('offersToApprove'))->render();

            Mail::to($email_to)->cc($email_cc)->send(new SendGeneralEmail($email_from, $email_subject, $email_body));

            $this->info('Successfully sent offers to approve to admin.');
        } else {
            $this->info('There are not offers to approve.');
        }
    }
}
