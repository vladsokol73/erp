<?php

namespace Database\Seeders;

use App\Models\Ticket\PlayerTicket;
use App\Models\User\User;
use Illuminate\Database\Seeder;

class PlayerTicketSeeder extends Seeder
{
    public function run(): void
    {
        //if (User::query()->count() === 0) {
       //     User::factory(10)->create();
        //}

        PlayerTicket::factory()->count(50)->create();
    }
}


