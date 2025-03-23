<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UpdatedEmailNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $urlHome;
    public $urlPassword;

    public function __construct($userName, $urlHome, $urlPassword)
    {
        $this->userName = $userName;
        $this->urlHome = $urlHome;
        $this->urlPassword = $urlPassword;
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Votre adresse email a été mise à jour');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.updated_email');
    }
}
