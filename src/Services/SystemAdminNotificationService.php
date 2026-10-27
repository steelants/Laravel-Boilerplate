<?php

namespace SteelAnts\LaravelBoilerplate\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SystemAdminNotificationService
{
    /**
     * Mails boilerplate.system_admins_mail; a broken mailer is only logged.
     */
    public static function send(string $subject, string $message): void
    {
        $mails = array_filter((array) (config('boilerplate.system_admins_mail') ?: []));

        if (empty($mails)) {
            return;
        }

        // A broken mailer must never turn a finished job into a failed one, nor mask
        // the error that made it fail in the first place.
        try {
            Mail::raw($message, function ($mail) use ($mails, $subject) {
                $mail->to($mails)->subject($subject . ' ' . config('app.name'));
            });
            Log::info('Sending Notification');
        } catch (Throwable $e) {
            Log::error('System admin notification could not be sent: ' . $e->getMessage());
        }
    }
}
