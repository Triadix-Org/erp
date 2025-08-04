<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name' => 'Human Resources',
                'description' => 'Handles employee relations and recruitment.',
                'division_id' => Division::where('name', 'Operations')->first()->id,
            ],
            [
                'name' => 'Finance',
                'description' => 'Manages financial planning and accounting.',
                'division_id' => Division::where('name', 'Operations')->first()->id,
            ],
            [
                'name' => 'IT Support',
                'description' => 'Provides technical support and manages IT infrastructure.',
                'division_id' => Division::where('name', 'Customer Service')->first()->id,
            ],
            [
                'name' => 'Sales',
                'description' => 'Responsible for sales strategies and customer relationships.',
                'division_id' => Division::where('name', 'Marketing')->first()->id,
            ],
        ];

        foreach ($data as $department) {
            \App\Models\Department::create($department);
        }
    }
}
