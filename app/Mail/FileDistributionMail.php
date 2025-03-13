<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FileDistributionMail extends Mailable
{
    use Queueable, SerializesModels;
    //variable used in the mail:
    public $userName;
    public $projectName;
    public $senderName;
    public $address;
    public $files;
    public $downloadLink;

    /**
     * Create a new message instance.
     */
    //mail constuctor
    public function __construct($userName, $projectName, $senderName, $address,$files, $downloadLink)
    {
        $this->userName = $userName;
        $this->projectName = $projectName;
        $this->senderName = $senderName;
        $this->address = $address;
        $this->files = $files;
        $this->downloadLink = $downloadLink;
    }

    /**
     * Define the email envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "📁 Nouvelle demande de distribution - $this->projectName",
        );
    }

    /**
     * Define the email content.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.distribute_files',
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
