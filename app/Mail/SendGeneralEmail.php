<?php

namespace App\Mail;


use App\Models\Email;
use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Services\GraphService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;


class SendGeneralEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $email_from;

    public $email_subject;

    public $email_content;
    public $email_id;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email_from, $email_subject, $email_content, $email_id = null)
    {
        $this->email_from    = $email_from;
        $this->email_subject = $email_subject;
        $this->email_content = $email_content;
        $this->email_id = $email_id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->email_from, 'International Zoo Services')
            ->subject($this->email_subject)
            ->view('emails.send-email');
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
