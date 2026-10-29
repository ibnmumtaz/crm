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

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(PipelineSeeder::class);
        $this->seed(LeadSourceSeeder::class);
    }

    public function test_admin_pages_load(): void
    {
        $user = User::factory()->create();
        $user->assignRole('admin');
        $pipeline = Pipeline::first();
        $stage = PipelineStage::first();
        $lead = Lead::factory()->create(['owner_id' => $user->id, 'pipeline_id' => $pipeline->id, 'stage_id' => $stage->id]);
        $this->actingAs($user);
        foreach ([
            '/dashboard', '/leads', '/leads/create', '/leads/kanban', '/leads/export', '/leads/'.$lead->id, '/leads/'.$lead->id.'/edit',
            '/customers', '/customers/create',
            '/tasks', '/tasks/create',
            '/communications', '/communications/create',
            '/targets', '/targets/create',
            '/pipelines', '/pipelines/create', '/pipelines/'.$pipeline->id,
            '/reports', '/admin/users', '/admin/users/create', '/admin/teams', '/admin/teams/create', '/admin/settings', '/admin/bookings',
            '/book', '/contact', '/pricing', '/features', '/about', '/faq', '/privacy', '/terms',
        ] as $uri) {
            $r = $this->get($uri);
            if ($r->getStatusCode() >= 400) {
                dump($uri, $r->getStatusCode());
            }
            $this->assertLessThan(400, $r->getStatusCode(), $uri);
        }
    }
}
