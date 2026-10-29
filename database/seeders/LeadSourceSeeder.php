<?php

namespace Database\Seeders;

use App\Models\LeadSource;
use Illuminate\Database\Seeder;

class LeadSourceSeeder extends Seeder
{
    public function run(): void
    {
        $sources = ['Website', 'Referral', 'Cold Call', 'Event', 'Other'];
        foreach ($sources as $source) {
            LeadSource::firstOrCreate(['name' => $source], ['is_active' => true]);
        }
    }
}
