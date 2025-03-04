<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewUser extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $urlHome;
    public $password;
    public $urlPassword;

    /**
     * Create a new message instance.
     */
    public function __construct($userName, $urlHome, $password, $urlPassword)
    {
        $this->userName = $userName;
        $this->urlHome = $urlHome;
        $this->password = $password;
        $this->urlPassword = $urlPassword;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre compte a été créé !',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new_user',
        );
    }
}
