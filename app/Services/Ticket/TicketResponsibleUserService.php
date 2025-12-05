<?php

namespace App\Services\Ticket;

use App\DTO\Ticket\TicketResponsibleUserDto;
use App\Models\Ticket\TicketResponsibleUser;
use App\Models\Ticket\TicketTopic;

class TicketResponsibleUserService
{
    public function getResponsible(int $responsibleId): TicketResponsibleUser
    {
        return TicketResponsibleUser::query()->findOrFail($responsibleId);
    }

    public function assignApprovalToTopic(TicketTopic $topic, TicketResponsibleUserDto $dto): TicketResponsibleUser
    {
        $approval = $topic->approval;

        $data = [
            'responsible_type'  => $this->resolveResponsibleType($dto->responsible_model_name),
            'responsible_id'    => $dto->responsible_id,
            'responsible_title' => $dto->responsible_title,
        ];

        if ($approval) {
            $approval->update($data);
        } else {
            $approval = TicketResponsibleUser::create([
                ...$data,
                'source'    => 'topic_approval',
                'source_id' => $topic->id,
            ]);
            $topic->update(['approval_id' => $approval->id]);
        }

        return $approval;
    }

    public function syncResponsiblesForTopic(TicketTopic $topic, array $responsibles): void
    {
        // Удаляем предыдущие связанные записи, если они есть
        TicketResponsibleUser::where('source', 'topic')
            ->where('source_id', $topic->id)
            ->delete();

        // Добавляем новые записи
        foreach ($responsibles as $dto) {
            TicketResponsibleUser::create([
                'source'            => 'topic',
                'source_id'         => $topic->id,
                'responsible_type'  => $this->resolveResponsibleType($dto->responsible_model_name),
                'responsible_id'    => $dto->responsible_id,
                'responsible_title' => $dto->responsible_title,
            ]);
        }
    }

    public function syncResponsiblesForApproval(TicketTopic $topic, array $responsibles): void
    {
        // Удаляем предыдущие связанные записи, если они есть
        TicketResponsibleUser::where('source', 'topic_approval')
            ->where('source_id', $topic->id)
            ->delete();

        // Добавляем новые записи
        foreach ($responsibles as $dto) {
            TicketResponsibleUser::create([
                'source'            => 'topic_approval',
                'source_id'         => $topic->id,
                'responsible_type'  => $this->resolveResponsibleType($dto->responsible_model_name),
                'responsible_id'    => $dto->responsible_id,
                'responsible_title' => $dto->responsible_title,
            ]);
        }
    }

    protected function resolveResponsibleType(?string $shortName): ?string
    {
        return match ($shortName) {
            'User'       => \App\Models\User\User::class,
            'Role'       => \App\Models\User\Role::class,
            'Permission' => \App\Models\User\Permission::class,
            default      => null,
        };
    }

}
