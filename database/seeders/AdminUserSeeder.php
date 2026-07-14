<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@scm.test'],
            [
                'name' => 'Admin SCM',
                'password' => Hash::make('password123'),
            ]
        );

        $this->command->info('Akun admin dibuat: admin@scm.test / password123');
    }
}
