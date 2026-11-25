<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed default Superadmin user with Bcrypt password
        User::firstOrCreate(
            ['email' => 'admin@metascout.gg'],
            [
                'name' => 'Superadmin',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'admin123')),
            ]
        );

        $this->call([
            HeroSeeder::class,
            DatasetSeeder::class,
        ]);
    }
}
