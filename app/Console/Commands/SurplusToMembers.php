<?php

namespace App\Console\Commands;

use App\Mail\SendGeneralEmail;
use App\Models\Contact;
use App\Models\Surplus;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SurplusToMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:surplus-to-members';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check every day if there are new surplus for our members.';

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
        $date             = Carbon::now()->subDay()->format('Y-m-d');
        $surplusToMembers = Surplus::whereDate('to_members_date', '=', $date)
            ->where('to_members', 1)
            ->get();

        $members = Contact::IsApproved()
            ->where('mailing_category', 'all_mailings')
            ->get();

        if (count($surplusToMembers) > 0) {
            $email_from    = 'info@zoo-services.com';
            $email_subject = 'New surplus specimens of today.';

            $email_body = view('emails.surplus-to-members', compact('surplusToMembers'))->render();
            Mail::to('izs@zoo-services.com')->send(new SendGeneralEmail($email_from, $email_subject, $email_body));

            foreach ($members as $contact) {
                $email_to = trim($contact->email);
                if ($email_to != null && $email_to != '') {
                    $email_body = view('emails.surplus-to-members', compact('contact', 'surplusToMembers'))->render();

                    Mail::to($email_to)->send(new SendGeneralEmail($email_from, $email_subject, $email_body));
                }
            }

            $this->info('Successfully sent surplus email to members.');
        } else {
            $this->info('There are not surplus to members.');
        }
    }
}
