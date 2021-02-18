<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\Models\User::firstOrCreate([
            'email' => 'admin@goacs.net',
        ],
        [
            'name' => 'admin',
            'password' => Hash::make('admin'),
            'email_verified_at' => now(),
        ]);

        $this->call(TaskSeeder::class);

    }
}
