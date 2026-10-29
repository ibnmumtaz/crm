<?php

namespace Database\Factories;

use App\Models\LeadSource;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadSourceFactory extends Factory
{
    protected $model = LeadSource::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'is_active' => true,
        ];
    }
}
