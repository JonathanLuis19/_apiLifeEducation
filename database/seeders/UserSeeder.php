<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert(
            [
                'rolee_id' => 1,
                'genre_id' => 1,
                'name' => 'Life Jac - LifeEducation',
                'user' => 'admin',
                'fecha_nacimiento' => '2021-02-15',
                'phone' => '123456789',
                'fecha_registro' => '2025-02-15',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin'),

            ]
        );
    }
}
