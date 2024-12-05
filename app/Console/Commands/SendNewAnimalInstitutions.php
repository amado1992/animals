<?php

namespace App\Console\Commands;

use App\Mail\SendGeneralEmail;
use App\Models\OrganisationSendAnimalNew;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendNewAnimalInstitutions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:new-animal-institutions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the mail with the new animals to the level institutions';

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
        $sends = OrganisationSendAnimalNew::all();
        if (!empty($sends->toArray())) {
            $email_send = [];
            $total      = $sends->count();
            foreach ($sends->toArray() as $key => $row) {
                $email_from    = $row['email_from'];
                $email_subject = $row['email_subject'];
                $email_body    = $row['email_body'];
                $email         = $row['email'];
                $name          = $row['name'];
                $email_body    = str_replace('[name_client]', $name, $email_body);
                Mail::to($email)->send(new SendGeneralEmail($email_from, $email_subject, html_entity_decode($email_body)));
                $send = OrganisationSendAnimalNew::find($row['id']);
                if (!empty($send->toArray())) {
                    $send->delete();
                }
                $email_send[$key] = ['name' => $name, 'email' => $email];
                if ($total === $key + 1) {
                    $this->sendEmailValueNewAnimal($email_send);
                }
                usleep(100000);
            }
        }
    }

    public function sendEmailValueNewAnimal($send_emails)
    {
        if (!empty($send_emails)) {
            $email_from    = 'info@zoo-services.com';
            $email_subject = 'List of emails that the list of animals was sent';
            $email_title   = 'List of emails that the list of animals was sent';
            $email_body    = view('emails.list-emails-animal-send', compact('send_emails', 'email_title'))->render();
            Mail::to('johnrens@zoo-services.com')->send(new SendGeneralEmail($email_from, $email_subject, $email_body));
        }
    }
}
