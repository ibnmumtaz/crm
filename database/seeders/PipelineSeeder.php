<?php

namespace Database\Seeders;

use App\Models\Pipeline;
use App\Models\PipelineStage;
use Illuminate\Database\Seeder;

class PipelineSeeder extends Seeder
{
    public function run(): void
    {
        $pipeline = Pipeline::firstOrCreate(
            ['name' => 'Default Sales Pipeline'],
            ['is_default' => true, 'is_active' => true],
        );

        $stages = [
            ['name' => 'New', 'order' => 1, 'probability' => 10, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Contacted', 'order' => 2, 'probability' => 25, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Interested', 'order' => 3, 'probability' => 50, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Negotiating', 'order' => 4, 'probability' => 75, 'is_won' => false, 'is_lost' => false],
            ['name' => 'Won', 'order' => 5, 'probability' => 100, 'is_won' => true, 'is_lost' => false],
            ['name' => 'Lost', 'order' => 6, 'probability' => 0, 'is_won' => false, 'is_lost' => true],
        ];

        foreach ($stages as $stage) {
            PipelineStage::firstOrCreate(
                ['pipeline_id' => $pipeline->id, 'name' => $stage['name']],
                $stage,
            );
        }
    }
}
