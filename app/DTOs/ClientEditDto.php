<?php

namespace App\DTOs;

use App\Contracts\DTOs\FromRequestInterface;
use Illuminate\Http\Request;

class ClientEditDto extends BaseDto implements FromRequestInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone = null,
        public readonly ?string $company = null,
        public readonly ?string $comment = null
    ) {
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'comment' => $this->comment
        ];
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            name: $request->input('name'),
            email: $request->input('email'),
            phone: $request->input('phone'),
            company: $request->input('company'),
            comment: $request->input('comment')
        );
    }
}
