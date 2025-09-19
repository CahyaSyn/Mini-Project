<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CreateUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = [
            [
                'id' => 1,
                'name' => 'Admin Cahya',
                'email' => 'cahya@email.com',
                'password' => bcrypt('password_admin1!')
            ],
        ];

        foreach ($user as $key => $value) {
            User::create($value);
        }
    }
}
