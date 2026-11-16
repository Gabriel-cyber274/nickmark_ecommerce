<?php

namespace App\Observers;

use App\Models\Admin;
use App\Mail\AdminActionNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminActionObserver
{
    public function created($record): void
    {
        $this->notify($record, 'created');
    }

    public function updated($record): void
    {
        $this->notify($record, 'updated');
    }

    public function deleted($record): void
    {
        $this->notify($record, 'deleted');
    }

    protected function notify($record, string $action): void
    {
        $actor = Auth::guard('admin')->user();

        if (! $actor || ($actor instanceof Admin && $actor->isSuperAdmin())) {
            return;
        }

        $recipient = config('services.admin_email_action');

        if (! $recipient) {
            return;
        }

        $changes = [];

        if ($action === 'updated') {
            $ignore = ['updated_at', 'created_at', 'password', 'remember_token', 'views'];

            foreach ($record->getChanges() as $field => $new) {
                if (in_array($field, $ignore, true)) {
                    continue;
                }

                $changes[$field] = [
                    'old' => $record->getOriginal($field),
                    'new' => $new,
                ];
            }

            if (empty($changes)) {
                return;
            }
        }

        Mail::to($recipient)->queue(new AdminActionNotification(
            actorName: $actor->name,
            actorEmail: $actor->email,
            action: $action,
            model: class_basename($record),
            recordId: (int) $record->getKey(),
            changes: $changes,
            recordTitle: $record->name ?? null,
        ));
    }
}
