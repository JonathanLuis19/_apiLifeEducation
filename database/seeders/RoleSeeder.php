<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert(
            [
                [
                    'rol' => 'admin',
                    'description' => 'User is allowed to manage and edit other users',
                ],
                [
                    'rol' => 'teacher',
                    'description' => 'User is allowed to manage only his own data',
                ],
                [
                    'rol' => 'student',
                    'description' => 'User is allowed to manage only his own data',
                ],
            ],
        );
    }
}
