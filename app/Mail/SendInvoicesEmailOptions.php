<?php

namespace App\Mail;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendInvoicesEmailOptions extends Mailable
{
    use Queueable, SerializesModels;

    public $invoices;

    public $email_option;

    public $email_from;

    public $email_subject;

    public $email_content;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invoice, $email_option, $email_from, $email_subject, $email_content)
    {
        $this->invoice       = $invoice;
        $this->email_option  = $email_option;
        $this->email_from    = $email_from;
        $this->email_subject = $email_subject;
        $this->email_content = $email_content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $fileName           = $this->invoice[0]->invoice_file;
        $folderNameMultiple = $this->invoice[0]['order']['full_number'];
        $this->from($this->email_from, 'International Zoo Services')
            ->subject($this->email_subject)
            ->view('emails.send-email');
        foreach ($this->invoice as $row) {
            $folderNameMultiple = $row->order->full_number;
            $fileName           = $row->invoice_file;
            $this->attachFromStorage('public/orders_docs/' . $folderNameMultiple . '/outgoing_invoices/' . $fileName);
        }

        return $this;
    }
}
