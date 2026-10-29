<?php

namespace Database\Factories;

use App\Models\Communication;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommunicationFactory extends Factory
{
    protected $model = Communication::class;

    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'user_id' => User::factory(),
            'type' => 'note',
            'subject' => $this->faker->sentence(4),
            'body' => $this->faker->paragraph(),
            'outcome' => null,
            'occurred_at' => now(),
        ];
    }

    public function forLead(Lead $lead): static
    {
        return $this->state(fn () => ['lead_id' => $lead->id]);
    }
}
