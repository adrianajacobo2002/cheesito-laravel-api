<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nombre' => 'Admin',
            'apellido' => 'General',
            'email' => 'admin@cheesito.com',
            'password' => 'admin123',
            'rol' => 'admin',
        ]);

        User::create([
            'nombre' => 'Juan',
            'apellido' => 'Figueroa',
            'email' => 'mesero@cheesito.com',
            'password' => 'mesero123',
            'rol' => 'mesero',
        ]);

        User::create([
            'nombre' => 'Carlos',
            'apellido' => 'Linguini',
            'email' => 'chef@cheesito.com',
            'password' => 'chef123',
            'rol' => 'cocinero',
        ]);
    }
}
