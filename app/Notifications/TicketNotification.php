<?php

namespace App\Notifications;

use App\Notifications\Channels\TelegramChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;


class TicketNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

    protected $ticket;
    protected $type;
    protected $channels;

    public function __construct($type, $ticket = null, $channels = null)
    {
        $this->channels = $channels ?? ['database', 'telegram'];
        $this->ticket = $ticket;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        $channels = $this->channels;

        if (in_array('telegram', $channels) && !$notifiable->activeTelegramIntegrations()) {
            $channels = array_diff($channels, ['telegram']);
        }

        // Меняем строку "telegram" на класс TelegramChannel
        return array_map(fn($channel) => $channel === 'telegram' ? TelegramChannel::class : $channel, $channels);
    }

    /**
     * Get the database representation of the notification.
     */
    // Формируем уведомление для базы данных
    public function toDatabase($notifiable): array
    {
        return [
            'ticket_id' => $this->ticket?->id,
            'message' => $this->getMessage(),
        ];
    }

    // Метод для генерации текста уведомления
    private function getMessage(): string
    {
        $ticketNumber = $this->ticket?->ticket_number ?? 'unknown';

        return match ($this->type) {
            'connected' => 'Telegram notifications connected!',
            'to_approve' => 'You have new ticket to approve: ' . $ticketNumber,
            'updated' => 'Ticket ' . $ticketNumber . 'status updated, new status: ' . ($this->ticket?->status() ?? 'unknown'),
            'approved' => 'Your ticket ' . $ticketNumber . ' has been approved.',
            'todo' => "Tou have new ticket to do: " . $ticketNumber,
            'completed' => 'Ticket ' . $ticketNumber . ' was completed!',
            'declined' => 'Ticket ' . $ticketNumber . ' was declined!',
            'comment' => 'New comment under your ticket: ' . $ticketNumber,
            'status_updated' => 'Status of ticket ' . $ticketNumber . ' updated!',
            default => 'Ticket Updated: ' . $ticketNumber,
        };
    }

    public function toTelegram($notifiable): string
    {
        return $this->getMessage();
    }
}
