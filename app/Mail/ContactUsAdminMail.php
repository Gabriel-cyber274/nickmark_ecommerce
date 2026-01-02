<?php

namespace App\Mail;

use App\Models\ContactUs;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactUsAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public ContactUs $contact;

    public function __construct(ContactUs $contact)
    {
        $this->contact = $contact;
    }

    public function build()
    {
        return $this
            ->subject('New Contact Us Message')
            ->view('emails.contact-us-admin');
    }
}
