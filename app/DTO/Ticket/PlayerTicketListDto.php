<?php

namespace App\DTO\Ticket;

use App\Contracts\DTOs\FromCollectionInterface;
use App\Contracts\DTOs\FromModelInterface;
use App\DTO\CommentDto;
use App\DTO\Log\ProductLogListDto;
use App\DTO\User\UserDto;
use App\Enums\PlayerTicketStatusEnum;
use App\Models\Ticket\PlayerTicket;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PlayerTicketListDto implements FromModelInterface, FromCollectionInterface
{
    public function __construct(
        public readonly int $id,
        public readonly string $ticket_number,
        #[LiteralTypeScriptType('PlayerTicketStatusDto')]
        public readonly PlayerTicketStatusDto $status,
        #[LiteralTypeScriptType('App.DTO.User.UserDto')]
        public readonly UserDto $user,
        public readonly int $player_id,
        public readonly string $type,
        public readonly int $tg_id,
        public readonly bool $is_valid_tg_id,
        public readonly string $screen_url,
        public readonly string $sum,
        public readonly bool $is_valid_sum,
        public readonly string|null $approved_at = null,
        public readonly string|null $result = null,
        public readonly string $created_at,
        #[LiteralTypeScriptType('CommentDto[]')]
        public readonly array $comments = [],
        #[LiteralTypeScriptType('ProductLogListDto[]')]
        public readonly array $product_logs = [],
    ) {}

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof PlayerTicket)) {
            throw new \InvalidArgumentException('Expected PlayerTicket instance');
        }

        $approvedAt = null;
        if ($model->approved_at instanceof \Carbon\CarbonInterface) {
            $approvedAt = $model->approved_at->format('Y-m-d H:i:s');
        } elseif (!empty($model->approved_at)) {
            $approvedAt = (string) $model->approved_at;
        }

        return new self(
            id: $model->id,
            ticket_number: $model->ticket_number,
            status: new PlayerTicketStatusDto(
                name: $model->status,
                color: PlayerTicketStatusEnum::tryFrom($model->status)?->color()
            ),
            user: UserDto::fromModel($model->user()->withTrashed()->first()),
            player_id: $model->player_id,
            type: $model->type,
            tg_id: $model->tg_id,
            is_valid_tg_id: $model->is_valid_tg_id,
            screen_url: $model->screen_url,
            sum: (string) $model->sum,
            is_valid_sum: $model->is_valid_sum,
            approved_at: $approvedAt,
            result: $model->result,
            created_at: $model->created_at->format('Y-m-d H:i:s'),
            comments: CommentDto::fromCollection($model->comments),
            product_logs: ProductLogListDto::fromCollection($model->productLogs)
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ticket_number' => $this->ticket_number,
            'status' => $this->status,
            'user' => $this->user,
            'player_id' => $this->player_id,
            'type' => $this->type,
            'tg_id' => $this->tg_id,
            'is_valid_tg_id' => $this->is_valid_tg_id,
            'screen_url' => $this->screen_url,
            'sum' => $this->sum,
            'is_valid_sum' => $this->is_valid_sum,
            'approved_at' => $this->approved_at,
            'result' => $this->result,
            'created_at' => $this->created_at,
            'comments' => $this->comments,
            'product_logs' => $this->product_logs,
        ];
    }

    public static function fromCollection(Collection $collection): array
    {
        return $collection->map(fn($item) => static::fromModel($item))->toArray();
    }
}


