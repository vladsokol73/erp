<?php

namespace App\Services;

use App\DTO\NotificationDto;
use App\Models\Ticket\PlayerTicket;
use App\Models\Ticket\Ticket;
use App\Models\Ticket\TicketStatus;
use App\Models\User\Permission;
use App\Models\User\Role;
use App\Models\User\User;
use App\Notifications\TicketNotification;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    public function getUserNotifications(int $limit = 10, ?User $user = null): array
    {
        $user ??= Auth::user();

        $notifications = $user->notifications()
            ->latest()
            ->limit($limit)
            ->get();

        return NotificationDto::fromCollection($notifications);
    }

    public function markAllAsRead(?User $user = null): void
    {
        $user ??= Auth::user();

        $user->unreadNotifications->markAsRead();
    }

    public function markAsReadById(string $notificationId, ?User $user = null): void
    {
        $user ??= Auth::user();

        $notification = $user->notifications()->find($notificationId);

        if (!$notification) {
            throw new \RuntimeException("Notification not found or does not belong to user.");
        }

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }
    }

    public function notifyTicketApproval(Ticket $ticket): void
    {
        $approvals = $ticket->topic->approvalUsers;

        foreach ($approvals as $approval) {
            $users = collect();
            $model = $approval->responsible;
            if ($model instanceof User) {
                $users->push($model);
            } elseif ($model instanceof Role || $model instanceof Permission) {
                $users = $users->merge($model->users);
            }

            foreach ($users as $user) {
                $user->notify(new TicketNotification("to_approve", $ticket));
            }
        }
    }

    public function notifyCommentTicket(int $userId, Ticket $ticket): void
    {
        if ($userId != $ticket->user_id) {
            $ticket->user->notify(new TicketNotification("comment", $ticket));
        } else {
            $responsibleUsers = $ticket->topic->responsibleUsers;

            foreach ($responsibleUsers as $responsible) {
                $users = collect();
                $model = $responsible->responsible;
                if ($model instanceof User) {
                    $users->push($model);
                } elseif ($model instanceof Role || $model instanceof Permission) {
                    $users = $users->merge($model->users);
                }

                foreach ($users as $user) {
                    $user->notify(new TicketNotification("comment", $ticket));
                }
            }
        }
    }

    public function notifyCommentPlayerTicket(int $userId, PlayerTicket $ticket): void
    {
        if ($userId != $ticket->user_id) {
            $ticket->user->notify(new TicketNotification("comment", $ticket));
        }
    }

    public function handleTicketStatusChange(Ticket $ticket, TicketStatus $oldStatus, TicketStatus $newStatus, User $user): void
    {
        if ($oldStatus->is_approval && !$newStatus->is_final) {
            $this->notifyTicketApproved($user, $ticket);
            $this->notifyTicketTodo($ticket);
        } elseif ($oldStatus->is_final && $newStatus->is_final) {
            $this->notifyTicketDeclined($user, $ticket);
        } elseif (!$oldStatus->is_approval && $newStatus->is_final) {
            $this->notifyTicketComplete($user, $ticket);
        } else {
            $this->notifyTicketStatusUpdated($user, $ticket);
        }
    }


    private function notifyTicketApproved(User $user, Ticket $ticket): void
    {
        $user->notify(new TicketNotification("approved", $ticket));
    }

    private function notifyTicketDeclined(User $user, Ticket $ticket): void
    {
        $user->notify(new TicketNotification("declined", $ticket));
    }

    private function notifyTicketTodo(Ticket $ticket): void
    {
        $responsibleUsers = $ticket->topic->responsibleUsers;

        foreach ($responsibleUsers as $responsible) {
            $users = collect();
            $model = $responsible->responsible;
            if ($model instanceof User) {
                $users->push($model);
            } elseif ($model instanceof Role || $model instanceof Permission) {
                $users = $users->merge($model->users);
            }

            foreach ($users as $user) {
                $user->notify(new TicketNotification("todo", $ticket));
            }
        }
    }

    private function notifyTicketComplete(User $user, Ticket $ticket): void
    {
        $user->notify(new TicketNotification("completed", $ticket));
    }

    private function notifyTicketStatusUpdated(User $user, Ticket $ticket): void
    {
        $user->notify(new TicketNotification("status_updated", $ticket));
    }
}
