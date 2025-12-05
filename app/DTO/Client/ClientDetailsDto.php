<?php

namespace App\DTO\Client;

use App\Contracts\DTOs\FromModelInterface;
use App\DTO\Creative\CreativeDto;
use App\Models\Client\Client;
use App\Services\Creative\CreativeService;
use App\Services\ThumbnailService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class ClientDetailsDto implements FromModelInterface
{
    public function __construct(
        public int $id,
        public ?string $clickid,
        public ?int $tg_id,
        public ?string $source_id,
        public ?string $prod_id,
        public ?string $player_id,
        public bool $reg,
        public bool $dep,
        public bool $redep,
        public ?string $reg_date,
        public ?string $dep_date,
        public ?string $redep_date,
        public ?float $dep_sum,
        public bool $is_pb,
        public ?string $is_pb_date,
        public ?string $pb_bot_name,
        public ?string $pb_last_mssg,
        public bool $pb_channelsub,
        public ?string $pb_channelsub_date,
        public bool $is_c2d,
        public ?string $is_c2d_date,
        public ?string $c2d_channel_id,
        public ?string $c2d_tags,
        public ?string $c2d_last_mssg,
        public ?string $geo_click,
        public ?string $lang,
        public ?string $type,
        public ?string $user_agent,
        public ?string $oc,
        public ?string $ver_oc,
        public ?string $model,
        public ?string $browser,
        public ?string $ip,
        public ?string $sub1,
        public ?string $sub2,
        public ?string $sub3,
        public ?string $sub4,
        public ?string $sub5,
        public ?string $sub6,
        public ?string $sub7,
        public ?string $sub8,
        public ?string $sub9,
        public ?string $sub10,
        public ?string $sub11,
        public ?string $sub12,
        public ?string $sub13,
        public ?string $sub14,
        public ?string $sub15,
        public ?string $c2d_client_id,

        public ?CreativeDto $creative = null
    ) {}

    public static function fromModel(Model $model): static
    {
        if (!($model instanceof Client)) {
            throw new \InvalidArgumentException('Expected Client type model');
        }

        $creativeService = app(CreativeService::class);

        $creativeDto = null;

        if ($model->sub11) {
            try {
                $creative = $creativeService->getCreativeByCode($model->sub11);
                $creativeDto = CreativeDto::fromModel($creative);
            } catch (ModelNotFoundException) {
                // оставляем $creativeDto = null
            }
        }

        return new self(
            id: $model->id,
            clickid: $model->clickid,
            tg_id: $model->tg_id,
            source_id: $model->source_id,
            prod_id: $model->prod_id,
            player_id: $model->player_id,
            reg: (bool) $model->reg,
            dep: (bool) $model->dep,
            redep: (bool) $model->redep,
            reg_date: optional($model->reg_date)?->toDateTimeString(),
            dep_date: optional($model->dep_date)?->toDateTimeString(),
            redep_date: optional($model->redep_date)?->toDateTimeString(),
            dep_sum: $model->dep_sum,
            is_pb: (bool) $model->is_pb,
            is_pb_date: optional($model->is_pb_date)?->toDateTimeString(),
            pb_bot_name: $model->pb_bot_name,
            pb_last_mssg: optional($model->pb_last_mssg)?->toDateTimeString(),
            pb_channelsub: (bool) $model->pb_channelsub,
            pb_channelsub_date: optional($model->pb_channelsub_date)?->toDateTimeString(),
            is_c2d: (bool) $model->is_c2d,
            is_c2d_date: optional($model->is_c2d_date)?->toDateTimeString(),
            c2d_channel_id: $model->c2d_channel_id,
            c2d_tags: $model->c2d_tags,
            c2d_last_mssg: optional($model->c2d_last_mssg)?->toDateTimeString(),
            geo_click: $model->geo_click,
            lang: $model->lang,
            type: $model->type,
            user_agent: $model->user_agent,
            oc: $model->oc,
            ver_oc: $model->ver_oc,
            model: $model->model,
            browser: $model->browser,
            ip: $model->ip,
            sub1: $model->sub1,
            sub2: $model->sub2,
            sub3: $model->sub3,
            sub4: $model->sub4,
            sub5: $model->sub5,
            sub6: $model->sub6,
            sub7: $model->sub7,
            sub8: $model->sub8,
            sub9: $model->sub9,
            sub10: $model->sub10,
            sub11: $model->sub11,
            sub12: $model->sub12,
            sub13: $model->sub13,
            sub14: $model->sub14,
            sub15: $model->sub15,
            c2d_client_id: $model->c2d_client_id,

            creative: $creativeDto
        );
    }
}
