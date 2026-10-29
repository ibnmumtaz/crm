<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function adminUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    public function test_task_creation(): void
    {
        $user = $this->adminUser();
        $response = $this->actingAs($user)->post('/tasks', [
            'title' => 'Test Task',
            'due_at' => now()->addDay()->format('Y-m-d\TH:i'),
            'priority' => 'medium',
        ]);
        $response->assertRedirect('/tasks');
        $this->assertDatabaseHas('tasks', ['title' => 'Test Task']);
    }

    public function test_task_completion(): void
    {
        $user = $this->adminUser();
        $task = Task::factory()->create([
            'assignee_id' => $user->id,
            'creator_id' => $user->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->post("/tasks/{$task->id}/complete", []);
        $response->assertRedirect();
        $task->refresh();
        $this->assertEquals('completed', $task->status->value ?? $task->status);
    }

    public function test_task_cancellation(): void
    {
        $user = $this->adminUser();
        $task = Task::factory()->create([
            'assignee_id' => $user->id,
            'creator_id' => $user->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->post("/tasks/{$task->id}/cancel", []);
        $response->assertRedirect();
        $task->refresh();
        $this->assertEquals('cancelled', $task->status->value ?? $task->status);
    }

    public function test_guest_cannot_create_task(): void
    {
        $response = $this->post('/tasks', ['title' => 'X', 'due_at' => now()->addDay()->format('Y-m-d\TH:i'), 'priority' => 'low']);
        $response->assertRedirect('/login');
    }
}
