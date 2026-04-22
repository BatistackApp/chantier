<?php

namespace App\Mail;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowProfitabilityAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Project $project,
        public float $marginPercentage,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "ALERTE RENTABILITE: Chantier {$this->project->reference}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.projects.low_profitability',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
