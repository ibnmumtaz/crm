<?php

namespace Database\Factories;

use App\Models\Pipeline;
use Illuminate\Database\Eloquent\Factories\Factory;

class PipelineFactory extends Factory
{
    protected $model = Pipeline::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true).' Pipeline',
            'is_default' => false,
            'is_active' => true,
        ];
    }
}
