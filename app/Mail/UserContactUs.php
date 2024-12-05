<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserContactUs extends Mailable
{
    use Queueable, SerializesModels;

    public $contact;

    public $department;

    public $message;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Contact $contact, $department, $message)
    {
        $this->contact    = $contact;
        $this->department = $department;
        $this->message    = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('request@zoo-services.com', 'International Zoo Services')
            ->subject('Contact-us made.')
            ->view('emails.contact-us');
    }
}
