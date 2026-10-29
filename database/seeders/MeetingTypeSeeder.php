<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeetingTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Product Demonstration', 'description' => 'A guided walkthrough of the CRM platform.', 'duration_minutes' => 30],
            ['name' => 'Discovery Call', 'description' => 'Discuss your sales process and requirements.', 'duration_minutes' => 30],
            ['name' => 'Consultation', 'description' => 'Strategic consultation with our team.', 'duration_minutes' => 45],
        ];

        foreach ($types as $type) {
            DB::table('meeting_types')->updateOrInsert(['name' => $type['name']], array_merge($type, ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}
