<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\User;
use Database\Seeders\LeadSourceSeeder;
use Database\Seeders\PipelineSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(PipelineSeeder::class);
        $this->seed(LeadSourceSeeder::class);
    }

    private function adminUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    private function repUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('sales-rep');

        return $user;
    }

    public function test_admin_can_view_leads(): void
    {
        $user = $this->adminUser();
        $response = $this->actingAs($user)->get('/leads');
        $response->assertStatus(200);
    }

    public function test_rep_cannot_view_other_reps_lead(): void
    {
        $rep1 = $this->repUser();
        $rep2 = $this->repUser();
        $pipeline = Pipeline::first();
        $stage = PipelineStage::first();
        $lead = Lead::factory()->create([
            'owner_id' => $rep2->id,
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stage->id,
        ]);

        $response = $this->actingAs($rep1)->get("/leads/{$lead->id}");
        $response->assertStatus(403);
    }

    public function test_lead_creation(): void
    {
        $user = $this->adminUser();
        $pipeline = Pipeline::first();
        $stage = PipelineStage::first();

        $response = $this->actingAs($user)->post('/leads', [
            'name' => 'Test Lead',
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stage->id,
        ]);
        $response->assertRedirect('/leads');
        $this->assertDatabaseHas('leads', ['name' => 'Test Lead']);
    }

    public function test_lead_update(): void
    {
        $user = $this->adminUser();
        $pipeline = Pipeline::first();
        $stage = PipelineStage::first();
        $lead = Lead::factory()->create([
            'owner_id' => $user->id,
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stage->id,
        ]);

        $response = $this->actingAs($user)->put("/leads/{$lead->id}", [
            'name' => 'Updated Lead',
        ]);
        $response->assertRedirect('/leads');
        $this->assertDatabaseHas('leads', ['id' => $lead->id, 'name' => 'Updated Lead']);
    }

    public function test_lead_soft_delete(): void
    {
        $user = $this->adminUser();
        $pipeline = Pipeline::first();
        $stage = PipelineStage::first();
        $lead = Lead::factory()->create([
            'owner_id' => $user->id,
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stage->id,
        ]);

        $response = $this->actingAs($user)->delete("/leads/{$lead->id}");
        $response->assertRedirect('/leads');
        $this->assertSoftDeleted('leads', ['id' => $lead->id]);
    }

    public function test_mass_assignment_protected(): void
    {
        $user = $this->adminUser();
        $pipeline = Pipeline::first();
        $stage = PipelineStage::first();
        $lead = Lead::factory()->create([
            'owner_id' => $user->id,
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stage->id,
        ]);

        $otherUser = $this->repUser();
        $this->actingAs($user)->put("/leads/{$lead->id}", [
            'name' => 'Hacked',
            'owner_id' => $otherUser->id,
        ]);
        $lead->refresh();
        $this->assertNotEquals($otherUser->id, $lead->owner_id);
    }
}
