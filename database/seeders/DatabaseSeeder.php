<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            PipelineSeeder::class,
            LeadSourceSeeder::class,
            MeetingTypeSeeder::class,
        ]);

        if (app()->environment(['local', 'development', 'testing'])) {
            $this->call(DemoDataSeeder::class);
        }

        if (app()->environment(['production']) && env('SEED_SUPER_ADMIN')) {
            $this->call(SuperAdminSeeder::class);
        }
    }
}
