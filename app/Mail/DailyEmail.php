<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $ownProjects;
    public $otherProjects;

    public function __construct($user, $ownProjects, $otherProjects)
    {
        $this->user = $user;
        $this->ownProjects = $ownProjects;
        $this->otherProjects = $otherProjects;
    }

    /**
     * Define the email envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "📁 Rapport Quotidien des Plans",
        );
    }

    /**
     * Define the email content.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.daily',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
