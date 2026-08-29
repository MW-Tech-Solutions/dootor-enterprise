<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class ContactEnquiry extends Mailable
{
    use Queueable, SerializesModels;

    public string $senderFirstName;
    public string $senderLastName;
    public string $senderEmail;
    public string $enquirySubject;
    public string $enquiryMessage;

    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        string $subject,
        string $message
    ) {
        $this->senderFirstName = $firstName;
        $this->senderLastName  = $lastName;
        $this->senderEmail     = $email;
        $this->enquirySubject  = $subject;
        $this->enquiryMessage  = $message;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Enquiry] ' . $this->enquirySubject,
            // Reply-To is set to the visitor's email so admin can reply directly
            replyTo: [
                new Address($this->senderEmail, $this->senderFirstName . ' ' . $this->senderLastName),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.contact-enquiry',
        );
    }
}
