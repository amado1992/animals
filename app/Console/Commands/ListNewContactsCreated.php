<?php

namespace App\Console\Commands;

use App\Mail\SendGeneralEmail;
use App\Models\Contact;
use App\Models\Dashboard;
use App\Models\Email;
use App\Models\ItemDashboard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ListNewContactsCreated extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:list-contacts-created';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send list of new contacts created';

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
        $contacts = Contact::where('new_contact', 1)
            ->orderBy("created_at", "DESC")
            ->get();

        if (count($contacts) > 0) {
            $email_from    = 'info@zoo-services.com';
            $email_subject = 'New inserted contacts';
            $email_title   = "New inserted contacts";

            $email_body = view('emails.list-contacts-new', compact('contacts', 'email_title'))->render();
            //Mail::to('johnrens@zoo-services.com')->send(new SendGeneralEmail($email_from, $email_subject, $email_body));
            $this->createSentEmail($email_subject, $email_from, 'johnrens@zoo-services.com', $email_body);

            $this->info('Successfully sent contacts email to members.');
        } else {
            $this->info('There are not contacts to send.');
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

        $dashboard = Dashboard::where("filter_data", "new_inserted_contacts_institutes")->first();
        if (!empty($dashboard)) {
            $new_item_dashboard                  = new ItemDashboard();
            $new_item_dashboard["itemable_id"]   = $new_email->id;
            $new_item_dashboard["itemable_type"] = "email";
            $new_item_dashboard["dashboard_id"]  = $dashboard->id;
            $new_item_dashboard->save();
        }
    }
}
