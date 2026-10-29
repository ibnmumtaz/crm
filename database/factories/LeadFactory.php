<?php

namespace Database\Factories;

use App\Enums\LeadStatus;
use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'owner_id' => User::factory(),
            'team_id' => Team::factory(),
            'pipeline_id' => Pipeline::factory(),
            'stage_id' => PipelineStage::factory(),
            'source_id' => null,
            'name' => $this->faker->name(),
            'company' => $this->faker->company(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'value' => $this->faker->randomFloat(2, 1000, 100000),
            'currency' => 'USD',
            'expected_close' => $this->faker->dateTimeBetween('now', '+90 days'),
            'status' => LeadStatus::Active,
            'lost_reason' => null,
            'last_contact_at' => now()->subDays($this->faker->numberBetween(0, 30)),
        ];
    }

    public function won(): static
    {
        return $this->state(fn () => ['status' => LeadStatus::Won]);
    }

    public function lost(): static
    {
        return $this->state(fn () => [
            'status' => LeadStatus::Lost,
            'lost_reason' => 'Price too high',
        ]);
    }

    public function forOwner(User $user): static
    {
        return $this->state(fn () => [
            'owner_id' => $user->id,
            'team_id' => $user->teams()->first()?->id,
        ]);
    }
}
