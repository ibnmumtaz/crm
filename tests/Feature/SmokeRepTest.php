<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\LeadSourceSeeder;
use Database\Seeders\PipelineSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeRepTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(PipelineSeeder::class);
        $this->seed(LeadSourceSeeder::class);
    }

    public function test_rep_pages_load(): void
    {
        $user = User::factory()->create();
        $user->assignRole('sales-rep');
        $pipeline = Pipeline::first();
        $stage = PipelineStage::first();
        $lead = Lead::factory()->create(['owner_id' => $user->id, 'pipeline_id' => $pipeline->id, 'stage_id' => $stage->id]);
        $task = Task::factory()->create(['assignee_id' => $user->id, 'creator_id' => $user->id]);
        $this->actingAs($user);
        foreach ([
            '/dashboard', '/leads', '/leads/'.$lead->id, '/tasks', '/tasks/'.$task->id, '/communications', '/targets', '/reports',
        ] as $uri) {
            $r = $this->get($uri);
            if ($r->getStatusCode() >= 400) {
                dump($uri, $r->getStatusCode());
            }
            $this->assertLessThan(400, $r->getStatusCode(), $uri);
        }
        // rep cannot access admin
        $this->get('/admin/users')->assertStatus(403);
    }
}
