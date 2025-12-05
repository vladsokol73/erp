<?php

namespace Database\Factories\Ticket;

use App\Models\Ticket\PlayerTicket;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlayerTicket>
 */
class PlayerTicketFactory extends Factory
{
    protected $model = PlayerTicket::class;

    public function definition(): array
    {
        $createdAt = $this->faker->dateTimeBetween('-30 days', 'now');
        $approved = $this->faker->boolean(50);

        return [
            'ticket_number' => 'PT-' . $this->faker->unique()->numerify('####'),
            'user_id' => User::query()->inRandomOrder()->value('id') ?? User::factory(),
            'status' => $this->faker->randomElement(['On Approve', 'Approved', 'Rejected']),
            'player_id' => $this->faker->numberBetween(1000, 9999),
            'type' => $this->faker->randomElement(['FD', 'RD']),
            'tg_id' => $this->faker->numberBetween(100000, 999999),
            'screen_url' => $this->faker->imageUrl(736, 736, 'abstract', true),
            'sum' => $this->faker->randomFloat(2, 10, 1000),
            'approved_at' => $approved ? $this->faker->dateTimeBetween($createdAt, 'now') : null,
            'result' => $approved ? $this->faker->randomElement(['ok', 'done']) : null,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }
}


