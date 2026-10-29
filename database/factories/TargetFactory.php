<?php

namespace Database\Factories;

use App\Enums\TargetPeriod;
use App\Enums\TargetType;
use App\Models\Target;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TargetFactory extends Factory
{
    protected $model = Target::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'team_id' => null,
            'type' => TargetType::Revenue,
            'period' => TargetPeriod::Monthly,
            'period_start' => now()->startOfMonth(),
            'period_end' => now()->endOfMonth(),
            'amount' => $this->faker->numberBetween(50000, 200000),
            'created_by' => User::factory(),
        ];
    }
}
