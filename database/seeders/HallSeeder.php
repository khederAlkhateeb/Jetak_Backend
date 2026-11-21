<?php

namespace Database\Seeders;

use App\Models\Hall;
use Illuminate\Database\Seeder;

class HallSeeder extends Seeder
{
    public function run(): void
    {
        $halls = [
            ['name' => 'Study Hall', 'type' => 'study'],
            ['name' => 'PlayStation Room', 'type' => 'playstation'],
            ['name' => 'Billiards Hall', 'type' => 'billiards'],
            ['name' => 'Tennis Court', 'type' => 'tennis'],
            ['name' => 'Event Hall', 'type' => 'event'],
        ];

        foreach ($halls as $hall) {
            Hall::firstOrCreate(['name' => $hall['name']], ['type' => $hall['type']]);
        }
    }
}