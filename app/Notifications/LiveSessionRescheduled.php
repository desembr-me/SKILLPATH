<?php

namespace App\Notifications;

use App\Models\LiveSession;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;

class LiveSessionRescheduled extends Notification
{
    public function __construct(
        protected LiveSession $liveSession,
        protected Carbon $oldStartsAt,
        protected Carbon $oldEndsAt,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'live_session_rescheduled',
            'live_session_id' => $this->liveSession->id,
            'learning_path_title' => $this->liveSession->learningPath->title,
            'session_title' => $this->liveSession->title,
            'old_starts_at' => $this->oldStartsAt->format('Y-m-d H:i'),
            'new_starts_at' => $this->liveSession->starts_at->format('Y-m-d H:i'),
            'message' => "Jadwal live class \"{$this->liveSession->title}\" pada course \"{$this->liveSession->learningPath->title}\" diubah dari {$this->oldStartsAt->format('d M Y, H:i')} menjadi {$this->liveSession->starts_at->format('d M Y, H:i')}.",
        ];
    }
}
