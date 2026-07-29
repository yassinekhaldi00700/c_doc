<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Demo accounts (password for all: "password").
     *
     * Only the admin + a few candidate accounts are seeded here — real
     * professors, departments, and research subjects come from
     * RealThesisSubjectsSeeder.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Amina El Idrissi',
            'email' => 'admin@euromed.test',
            'phone' => '+212600000001',
        ]);

        $candidates = [
            ['name' => 'Yassine Alaoui', 'email' => 'yassine.alaoui@example.test'],
            ['name' => 'Nadia Berrada', 'email' => 'nadia.berrada@example.test'],
            ['name' => 'Omar Chraibi', 'email' => 'omar.chraibi@example.test'],
        ];

        foreach ($candidates as $candidate) {
            User::factory()->candidate()->create([
                'name' => $candidate['name'],
                'email' => $candidate['email'],
            ]);
        }
    }
}
