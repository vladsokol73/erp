<?php

namespace App\DTOs;

use App\Contracts\DTOs\FromRequestInterface;
use Illuminate\Http\Request;

class UserEditDto extends BaseDto implements FromRequestInterface
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $password = null,
        public readonly ?int $role_id = null,
        public readonly ?int $timezone = 0,
        public readonly ?array $available_operators = ['all'],
        public readonly ?array $available_clients = ['all'],
        public readonly ?array $available_countries = ['all']
    ) {
    }

    public static function fromRequest(Request $request): static
    {
        return new self(
            name: $request->input('name'),
            email: $request->input('email'),
            password: $request->input('password'),
            role_id: $request->input('role_id'),
            timezone: $request->input('timezone', 0),
            available_operators: $request->input('available_operators', ['all']),
            available_clients: $request->input('available_clients', ['all']),
            available_countries: $request->input('available_countries', ['all'])
        );
    }

    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'timezone' => $this->timezone,
            'available_operators' => $this->available_operators,
            'available_clients' => $this->available_clients,
            'available_countries' => $this->available_countries
        ];

        if ($this->role_id) {
            $data['role_id'] = $this->role_id;
        }

        if ($this->password) {
            $data['password'] = bcrypt($this->password);
        }

        return $data;
    }
}
