<?php

namespace Hassan\Termii;

use Illuminate\Notifications\Notification;
use Hassan\Termii\Exceptions\TermiiException;

/**
 * Termii Notification Channel for Laravel's notification system.
 *
 * Usage:
 *   public function via($notifiable): array
 *   {
 *       return [TermiiChannel::class];
 *   }
 *
 *   public function toTermii($notifiable): TermiiMessage
 *   {
 *       return (new TermiiMessage)->content('Your OTP is 123456');
 *   }
 */
class TermiiChannel
{
    public function __construct(protected readonly Termii $termii) {}

    /**
     * Send the given notification.
     *
     * @throws TermiiException
     */
    public function send(mixed $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toTermii')) {
            return;
        }

        $message = $notification->toTermii($notifiable);

        if (! ($message instanceof TermiiMessage)) {
            return;
        }

        $to = $message->to ?? $notifiable->routeNotificationFor('termii', $notification);

        if (empty($to)) {
            return;
        }

        $this->termii->sendMessage(
            to: $to,
            sms: $message->content,
            from: $message->from,
            channel: $message->channel,
        );
    }
}
