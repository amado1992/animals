<?php

namespace App\Mail;

use App\Models\Attachment;
use App\Models\Email;
use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Services\GraphService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class SendOfferEmailOptions extends Mailable
{
    use Queueable, SerializesModels;

    public $offer;

    public $email_option;

    public $email_from;

    public $email_subject;

    public $email_content;

    public $email_id;
    public $attachment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Offer $offer, $email_option, $email_from, $email_subject, $email_content, $email_id = null, $attachment = null)
    {
        $this->offer         = $offer;
        $this->email_option  = $email_option;
        $this->email_from    = $email_from;
        $this->email_subject = $email_subject;
        $this->email_content = $email_content;
        $this->email_id = $email_id;
        $this->attachment = $attachment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $folderName = $this->offer->full_number;

        switch ($this->email_option) {
            case 'send_offer':
            case 'send_all':
            case 'remind_1':
            case 'remind_2':
            case 'remind_all':
                if (Storage::exists('public/offers_docs/' . $folderName . '/' . 'Offer ' . $folderName . '.pdf')) {
                    $this->createAttachments('public/offers_docs/'.$folderName.'/Offer ' . $folderName . '.pdf', 'Offer ' . $folderName . '.pdf', $this->email_id, "application/pdf");
                    return $this->from($this->email_from, 'International Zoo Services')
                        ->subject($this->email_subject)
                        ->view('emails.send-email')
                        ->attachFromStorage('public/offers_docs/' . $folderName . '/Offer ' . $folderName . '.pdf');
                } else {
                    return $this->from($this->email_from, 'International Zoo Services')
                        ->subject($this->email_subject)
                        ->view('emails.send-email');
                }
                break;
            default:
                return $this->from($this->email_from, 'International Zoo Services')
                    ->subject($this->email_subject)
                    ->view('emails.send-email');
                break;
        }
    }

    public function createAttachments($path, $name, $email, $type = null){
        $attachment = new Attachment();
        $attachment["path"] = $path;
        $attachment["name"] = $name;
        $attachment["type"] = $type;
        $attachment["email_id"] = $email;
        $attachment->save();

        $this->attachment = $attachment->toArray();
    }

    public function sendEmail($email_to = null, $options, $email_cc = null, $email_bcc = null){
        if (App::environment('production')) {
            $GraphService = new GraphService();
            $GraphService->initializeGraphForUserAuth();
            $user_acount = User::find(Auth::user()->id);
            if(!empty($user_acount)){
                $userToken = $GraphService->getAllUserToken($user_acount->id);
                if (empty($userToken)) {
                    $userToken = $GraphService->getAllUserTokenByEmail();
                }
            }else{
                $userToken = $GraphService->getAllUserTokenByEmail();
            }

            $email_to_array = [];
            if ($email_to != null)
                $email_to_array = array_map('trim', explode(',', $email_to));

            $email_cc_array = [];
            if ($email_cc != null)
                $email_cc_array = array_map('trim', explode(',', $email_cc));

            $email_bcc_array = [];
            if ($email_bcc != null)
                $email_bcc_array = array_map('trim', explode(',', $email_bcc));

            foreach ($userToken as $row){
                if($row["email"] == "test@zoo-services.com"){
                    $options->email_from = "info@zoo-services.com";
                }

                $token = $GraphService->getUserToken($row["id"], json_decode($row["token"]));
                $user_id = $GraphService->getUserByEmail($token, $options->email_from);
                if(!empty($token) && !empty($user_id)){
                    $send = $GraphService->saveEmailSystem($token, $user_id->getId(), $email_to_array, $options, $email_cc_array, $email_bcc_array);

                    if(!empty($send["id"])){
                        $GraphService->sendEmailDraft($token, $user_id->getId(), $send["id"]);
                        $email = Email::find($options->email_id);
                        $email["guid"] = $send["id"];
                        $email->save();
                    }
                }
                break;
            }

        }
    }
}
