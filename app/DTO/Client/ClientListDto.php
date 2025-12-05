<?php

namespace App\DTO\Client;

use App\Contracts\DTOs\FromModelInterface;
use App\Models\Client\Client;
use Illuminate\Database\Eloquent\Model;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ClientListDto implements FromModelInterface
{
    public function __construct(
        public int $id,                    // Client ID
        public string|null $clickid,       // Click ID
        public string|null $tg_id,         // Telegram ID
        public string|null $c2d_channel_id // C2D Channel ID
    ) {}

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof Client)) {
            throw new \InvalidArgumentException('Expected Client type model');
        }

        return new self(
            id: $model->id,
            clickid: $model->clickid,
            tg_id: $model->tg_id,
            c2d_channel_id: $model->c2d_channel_id
        );
    }
}
