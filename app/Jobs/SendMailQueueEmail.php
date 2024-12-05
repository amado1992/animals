<?php

namespace App\Jobs;

use App\Mail\SendGeneralEmail;
use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\SendGeneralEmail;
use App\Models\Contact;
use App\Models\Email;
use App\Models\Labels;
use App\Models\Organisation;
use Illuminate\Support\Facades\App;

class SendMailQueueEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 7200;

    protected $emailFrom;

    protected $emailSubject;

    protected $emailBody;

    protected $emailToArray;

    protected $emailCcArray;
    protected $surplu_id;
    protected $type;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email_from, $email_subject, $email_body, $email_to_array, $email_cc_array, $surplu_id = null, $type = null)
    {
        $this->emailFrom    = $email_from;
        $this->emailSubject = $email_subject;
        $this->emailBody    = $email_body;
        $this->emailToArray = $email_to_array;
        $this->emailCcArray = $email_cc_array;
        $this->surplu_id = $surplu_id;
        $this->type = $type;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $email_body = Str::of($this->emailBody)->replace('contact_name', 'IZS');
        $email_create = $this->createSentEmail($this->emailSubject, $this->emailFrom, 'izs@zoo-services.com', $email_body, $this->surplu_id);
        $email_options = new SendGeneralEmail($this->emailFrom, $this->emailSubject, $email_body, $email_create["id"]);
        if (App::environment('production')) {
            $email_options->sendEmail('izs@zoo-services.com', $email_options->build());
        }else{
            Mail::to('izs@zoo-services.com')->send(new SendGeneralEmail($this->emailFrom, $this->emailSubject, $email_body));
        }

        foreach ($this->emailToArray as $emailTo) {
            $email = trim($emailTo);
            if ($email != '') {
                $contact = Contact::GetContacts()->where('email', $email)->first();

                if ($contact != null) {
                    $email_body = Str::of($this->emailBody)->replace('contact_name', $contact->letter_name);
                    $email_create = $this->createSentEmail($this->emailSubject, $this->emailFrom, $email, $email_body, $this->surplu_id);
                    $email_options = new SendGeneralEmail($this->emailFrom, $this->emailSubject, $email_body, $email_create["id"]);
                    if (App::environment('production')) {
                        $email_options->sendEmail($email, $email_options->build());
                    }else{
                        Mail::to($email)->send(new SendGeneralEmail($this->emailFrom, $this->emailSubject, $email_body));
                    }
                }
            }
        }

        foreach ($this->emailCcArray as $emailCc) {
            $email = trim($emailCc);
            if ($email != '') {
                $contact = Contact::GetContacts()->where('email', $email)->first();

                if ($contact != null) {
                    $email_body = Str::of($this->emailBody)->replace('contact_name', $contact->letter_name);
                    $email_create = $this->createSentEmail($this->emailSubject, $this->emailFrom, $email, $email_body, $this->surplu_id);
                    $email_options = new SendGeneralEmail($this->emailFrom, $this->emailSubject, $email_body, $email_create["id"]);
                    if (App::environment('production')) {
                        $email_options->sendEmail($email, $email_options->build());
                    }else{
                        Mail::to($email)->send(new SendGeneralEmail($this->emailFrom, $this->emailSubject, $email_body));
                    }
                }
            }
        }
    }

    public function createSentEmail($subject, $from, $email, $body, $id = null, $type = null)
    {

        $label = Labels::where("name", "surplus")->first();
        $contact =  Contact::where("email", $email)->first();
        $new_email = new Email();
        if(!empty($contact) && $contact->count() > 0){
            $first_name = $contact["first_name"] ?? "";
            $last_name = $contact["last_name"] ?? "";
            $name = $first_name . " " . $last_name;
            $new_email["contact_id"] = $contact["id"] ?? null;
        }else{
            $organisation =  Organisation::where("email", $email)->first();
            $new_email["organisation_id"] = $organisation["id"] ?? null;
            $name = $organisation["name"] ?? "";
        }
        $new_email["from_email"] = $from;
        $new_email["to_email"] = $email;
        $new_email["body"] = $body;
        $new_email["guid"] = rand(1,100);
        $new_email["subject"] = $subject;
        $new_email["name"] = $name;
        if(!empty($id) && !empty($type)){
            if($type == "surplu"){
                $new_email["surplu_id"] = $id;
            }
            if($type == "wanted"){
                $new_email["wanted_id"] = $id;
            }
        }
        $new_email["is_send"] = 1;
        $new_email->save();
        $new_email->labels()->attach($label);

        return  $new_email;
    }
}
