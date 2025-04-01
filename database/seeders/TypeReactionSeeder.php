<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeReactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('type_reactions')->insert([
            [
                'name' => 'Me gusta',
                'img' => '👍', // Emoji de "Me gusta"
            ],
            [
                'name' => 'Me encanta',
                'img' => '😍', // Emoji de "Me encanta"
            ],
            [
                'name' => 'Me importa',
                'img' => '🤔', // Emoji de "Me importa"
            ],
            [
                'name' => 'Me divierte',
                'img' => '😂', // Emoji de "Me divierte"
            ],
            [
                'name' => 'Me asombra',
                'img' => '😲', // Emoji de "Me asombra"
            ],
            [
                'name' => 'Me entristece',
                'img' => '😢', // Emoji de "Me entristece"
            ],
            [
                'name' => 'Me enoja',
                'img' => '😠', // Emoji de "Me enoja"
            ],
        ]);
    }
}
