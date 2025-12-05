<?php

namespace App\DTOs;

use App\Contracts\DTOs\FromRequestInterface;
use App\Contracts\DTOs\ToArrayInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreativeEditDto extends BaseDto implements FromRequestInterface, ToArrayInterface
{
    public function __construct(
        public readonly string $code,
        public readonly string $url,
        public readonly string $type,
        public readonly ?string $resolution = null,
        public readonly ?int $country_id = null,
        public readonly ?int $user_id = null,
        public readonly ?array $tags_ids = []
    ) {
    }

    public static function fromRequest(Request $request): static
    {
        $tagsIds = [];
        $tags = $request->all();
        
        // Собираем ID тегов из запроса
        foreach ($tags as $key => $value) {
            if (strpos($key, 'tag_') === 0 && $value) {
                $tagsIds[] = (int)substr($key, 4);
            }
        }

        return new self(
            code: $request->input('code'),
            url: $request->input('url'),
            type: $request->input('type', 'image'),
            resolution: $request->input('resolution'),
            country_id: $request->input('country_id'),
            user_id: $request->input('user_id', Auth::id()),
            tags_ids: $tagsIds
        );
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'url' => $this->url,
            'type' => $this->type,
            'resolution' => $this->resolution,
            'country_id' => $this->country_id,
            'user_id' => $this->user_id,
            'tags_ids' => $this->tags_ids
        ];
    }
}
