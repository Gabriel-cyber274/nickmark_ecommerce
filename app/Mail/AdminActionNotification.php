<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminActionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $actorName,
        public string $actorEmail,
        public string $action,
        public string $model,
        public int $recordId,
        public array $changes = [],
        public ?string $recordTitle = null,
    ) {
    }

    public function build(): self
    {
        return $this
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->to(config('services.admin_email_action'))
            ->subject(sprintf(
                '[%s] Admin %s: %s %s #%d',
                config('app.name'),
                ucfirst($this->action),
                $this->model,
                $this->recordTitle ?? $this->recordId,
                $this->recordId,
            ))
            ->markdown('emails.admin-action', [
                'actorName' => $this->actorName,
                'actorEmail' => $this->actorEmail,
                'action' => $this->action,
                'model' => $this->model,
                'recordId' => $this->recordId,
                'recordTitle' => $this->recordTitle,
                'changes' => $this->changes,
            ]);
    }
}
