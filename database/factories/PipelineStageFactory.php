<?php

namespace Database\Factories;

use App\Models\Pipeline;
use App\Models\PipelineStage;
use Illuminate\Database\Eloquent\Factories\Factory;

class PipelineStageFactory extends Factory
{
    protected $model = PipelineStage::class;

    public function definition(): array
    {
        return [
            'pipeline_id' => Pipeline::factory(),
            'name' => $this->faker->word(),
            'order' => $this->faker->numberBetween(1, 6),
            'probability' => $this->faker->numberBetween(0, 100),
            'is_won' => false,
            'is_lost' => false,
        ];
    }
}
