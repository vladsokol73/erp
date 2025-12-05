<?php

namespace App\DTO\Shorter;

use App\Contracts\DTOs\FromRequestInterface;
use App\Contracts\DTOs\ToArrayInterface;
use Illuminate\Http\Request;

class DomainCreateDto implements FromRequestInterface, ToArrayInterface
{
    public function __construct(
        public readonly string $redirect_url,
        public readonly int|null $id = null,
        public readonly bool|null $is_active = null,
        public readonly string|null $domain = null,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new self(
            $request->get('id'),
            $request->get('redirect_url'),
            $request->get('is_active'),
            $request->get('domain'),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'original_url' => $this->redirect_url,
            'short_code' => $this->is_active,
            'domain' => $this->domain,
        ];
    }
}
