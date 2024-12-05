<?php

namespace App\Console\Commands;

use App\Mail\SendGeneralEmail;
use App\Models\Contact;
use App\Models\Dashboard;
use App\Models\Email;
use App\Models\ItemDashboard;
use App\Models\Labels;
use App\Models\Surplus;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class ListNewAnimalsCreated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:list-animals-created';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade mailinglist with newly inserted animals';

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
        $date    = Carbon::now();
        $date    = $date->subDay(120);
        $surplus = Surplus::where('new_animal', 1)
            ->where('origin', "!=", "stuffed")
            ->where('surplus_status', "!=", "collection")
            ->orderBy("created_at", "DESC")
            ->whereDate('surplus.created_at', '>=', $date->format('Y-m-d'))
            ->get();

        if (count($surplus) > 0) {
            $email_from      = 'info@zoo-services.com';
            $email_subject   = 'New inserted surplus suppliers';
            $email_title     = "New inserted surplus suppliers";
            $email_initially = "";
            $email_footer    = "";

            $email_body = view('emails.list-animal-new', compact('surplus', 'email_title', 'email_initially', 'email_footer'))->render();
            //Mail::to('johnrens@zoo-services.com')->send(new SendGeneralEmail($email_from, $email_subject, $email_body));
            $this->createSentEmail($email_subject, $email_from, 'johnrens@zoo-services.com', $email_body);

            $this->info('Successfully sent surplus email to members.');
        } else {
            $this->info('There are not animal to send.');
        }
    }

    public function createSentEmail($subject, $from, $email, $body)
    {
        $new_email               = new Email();
        $new_email["from_email"] = $from;
        $new_email["to_email"]   = $email;
        $new_email["body"]       = $body;
        $new_email["guid"]       = "";
        $new_email["subject"]    = $subject;
        $new_email["name"]       = "";
        $new_email["is_send"]    = 1;
        $new_email->save();

        $dashboard = Dashboard::where("filter_data", "new_inserted_surplus")->first();
        if (!empty($dashboard)) {
            $new_item_dashboard                  = new ItemDashboard();
            $new_item_dashboard["itemable_id"]   = $new_email->id;
            $new_item_dashboard["itemable_type"] = "email";
            $new_item_dashboard["dashboard_id"]  = $dashboard->id;
            $new_item_dashboard->save();
        }
    }
}
