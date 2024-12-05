<?php

namespace App\Mail;

use App\Models\Attachment;
use App\Models\Email;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Services\GraphService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SendOrderEmailOptions extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    public $invoice;

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
    public function __construct(Order $order, Invoice $invoice = null, $email_option, $email_from, $email_subject, $email_content, $email_id = null, $attachment = null)
    {
        $this->order         = $order;
        $this->invoice       = $invoice;
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
        $folderName = $this->order->full_number;

        switch ($this->email_option) {
            case 'reservation_supplier':
                $this->createAttachments('public/orders_docs/'.$folderName.'/Reservation supplier ' . $folderName . '.pdf', 'Reservation supplier ' . $folderName . '.pdf', $this->email_id, "application/pdf");
                return $this->from($this->email_from, 'International Zoo Services')
                            ->subject($this->email_subject)
                            ->view('emails.send-email')
                            ->attachFromStorage('public/orders_docs/'.$folderName.'/Reservation supplier ' . $folderName . '.pdf');

                break;
            case 'reservation_client':
                $this->createAttachments('public/orders_docs/'.$folderName.'/Reservation client ' . $folderName . '.pdf', 'Reservation client ' . $folderName . '.pdf', $this->email_id, "application/pdf");
                $this->createAttachments('public/orders_docs/'.$folderName.'/Checklist of documents ' . $folderName . '.pdf', 'Checklist of documents ' . $folderName . '.pdf', $this->email_id, "application/pdf");
                return $this->from($this->email_from, 'International Zoo Services')
                            ->subject($this->email_subject)
                            ->view('emails.send-email')
                            ->attachFromStorage('public/orders_docs/'.$folderName.'/Reservation client ' . $folderName . '.pdf')
                            ->attachFromStorage('public/orders_docs/'.$folderName.'/Checklist of documents ' . $folderName . '.pdf');
                break;
            case 'checklist_client':
                $this->createAttachments('public/orders_docs/'.$folderName.'/Checklist of documents ' . $folderName . '.pdf', 'Checklist of documents ' . $folderName . '.pdf', $this->email_id, "application/pdf");
                return $this->from($this->email_from, 'International Zoo Services')
                            ->subject($this->email_subject)
                            ->view('emails.send-email')
                            ->attachFromStorage('public/orders_docs/'.$folderName.'/Checklist of documents ' . $folderName . '.pdf');
                break;
            case 'proforma_invoice':
                $this->createAttachments('public/orders_docs/'.$folderName.'/Proforma invoice ' . $folderName . '.pdf', 'Proforma invoice ' . $folderName . '.pdf', $this->email_id, "application/pdf");
                return $this->from($this->email_from, 'International Zoo Services')
                            ->subject($this->email_subject)
                            ->view('emails.send-email')
                            ->attachFromStorage('public/orders_docs/'.$folderName.'/Proforma invoice ' . $folderName . '.pdf');
                break;
            case 'client_invoice':
                $fileName = $this->invoice->invoice_file;
                $this->createAttachments('public/orders_docs/'.$folderName.'/outgoing_invoices/'.$fileName, $fileName, $this->email_id, "application/pdf");
                return $this->from($this->email_from, 'International Zoo Services')
                            ->subject($this->email_subject)
                            ->view('emails.send-email')
                            ->attachFromStorage('public/orders_docs/'.$folderName.'/outgoing_invoices/'.$fileName);
                break;
            case 'statement_izs':
                $this->createAttachments('public/orders_docs/'.$folderName.'/Statement IZS '. $folderName . '.pdf', 'Statement IZS '. $folderName . '.pdf', $this->email_id, "application/pdf");
                return $this->from($this->email_from, 'International Zoo Services')
                            ->subject($this->email_subject)
                            ->view('emails.send-email')
                            ->attachFromStorage('public/orders_docs/'.$folderName.'/Statement IZS '. $folderName . '.pdf');
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
