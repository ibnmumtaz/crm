<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'lead_id' => Lead::factory(),
            'assignee_id' => User::factory(),
            'creator_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'notes' => $this->faker->optional()->sentence(),
            'priority' => TaskPriority::Medium,
            'status' => TaskStatus::Pending,
            'due_at' => now()->addDays($this->faker->numberBetween(1, 14)),
        ];
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'status' => TaskStatus::Overdue,
            'due_at' => now()->subDays(2),
        ]);
    }

    public function forLead(Lead $lead): static
    {
        return $this->state(fn () => ['lead_id' => $lead->id]);
    }
}
