<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Research and Development',
                'description' => 'Focuses on innovation and product development.',
            ],
            [
                'name' => 'Marketing',
                'description' => 'Handles advertising, promotions, and market research.',
            ],
            [
                'name' => 'Customer Service',
                'description' => 'Provides support and assistance to customers.',
            ],
            [
                'name' => 'Operations',
                'description' => 'Oversees daily business operations and logistics.',
            ],
        ];

        foreach ($data as $division) {
            \App\Models\Division::create($division);
        }
    }
}
