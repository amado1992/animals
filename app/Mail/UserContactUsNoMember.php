<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserContactUsNoMember extends Mailable
{
    use Queueable, SerializesModels;

    public $name;

    public $institution;

    public $email;

    public $country;

    public $mess;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $institution, $email, $country, $message)
    {
        $this->name        = $name;
        $this->institution = $institution;
        $this->email       = $email;
        $this->country     = $country;
        $this->mess        = $message;
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
            ->view('emails.contact-us-no-member');
    }
}
