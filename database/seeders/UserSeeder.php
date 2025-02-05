<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Anand',
                'email' => 'pawar@jasipa.com',
                'password' => bcrypt(12345678),
                'role' => 'Company admin'
            ],
            [
                'name' => 'user',
                'email' => 'user@jasipa.com',
                'password' => bcrypt(12345678),
                'role' => 'General user'
            ],
        ];

        User::insert($users);
    }
}
