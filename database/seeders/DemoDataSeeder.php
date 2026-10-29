<?php

namespace Database\Seeders;

use App\Enums\LeadStatus;
use App\Enums\TargetPeriod;
use App\Enums\TargetType;
use App\Models\Communication;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\Target;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Factory::create();

        $admin = User::firstOrCreate(
            ['email' => 'admin@demo.test'],
            ['name' => 'Demo Admin', 'password' => 'password', 'status' => 'active', 'email_verified_at' => now()]
        );
        $admin->syncRoles(['admin']);

        $manager = User::firstOrCreate(
            ['email' => 'manager@demo.test'],
            ['name' => 'Demo Manager', 'password' => 'password', 'status' => 'active', 'email_verified_at' => now()]
        );
        $manager->syncRoles(['sales-manager']);

        $analyst = User::firstOrCreate(
            ['email' => 'analyst@demo.test'],
            ['name' => 'Demo Analyst', 'password' => 'password', 'status' => 'active', 'email_verified_at' => now()]
        );
        $analyst->syncRoles(['analyst']);

        $reps = collect();
        for ($i = 1; $i <= 8; $i++) {
            $rep = User::firstOrCreate(
                ['email' => "rep{$i}@demo.test"],
                ['name' => "Demo Rep {$i}", 'password' => 'password', 'status' => 'active', 'email_verified_at' => now()]
            );
            $rep->syncRoles(['sales-rep']);
            $reps->push($rep);
        }

        $team = Team::firstOrCreate(['name' => 'Demo Sales Team'], ['manager_id' => $manager->id]);
        $team->users()->syncWithoutDetaching($reps->pluck('id')->all());
        $team->users()->syncWithoutDetaching([$manager->id]);

        if (Lead::count() > 0) {
            return;
        }

        $pipeline = Pipeline::where('is_default', true)->first() ?? Pipeline::first();
        $stages = PipelineStage::where('pipeline_id', $pipeline->id)->get();
        $sources = LeadSource::all();

        $leads = collect();
        for ($i = 0; $i < 50; $i++) {
            $owner = $reps->random();
            $stage = $stages->random();
            $lead = Lead::create([
                'owner_id' => $owner->id,
                'team_id' => $team->id,
                'pipeline_id' => $pipeline->id,
                'stage_id' => $stage->id,
                'source_id' => $sources->isNotEmpty() ? $sources->random()->id : null,
                'name' => $faker->name(),
                'company' => $faker->company(),
                'email' => $faker->unique()->safeEmail(),
                'phone' => $faker->phoneNumber(),
                'value' => $faker->randomFloat(2, 1000, 100000),
                'currency' => 'USD',
                'expected_close' => $faker->dateTimeBetween('now', '+90 days'),
                'status' => $stage->is_won ? LeadStatus::Won : ($stage->is_lost ? LeadStatus::Lost : LeadStatus::Active),
                'lost_reason' => $stage->is_lost ? 'No budget' : null,
                'last_contact_at' => now()->subDays($faker->numberBetween(0, 30)),
            ]);
            $leads->push($lead);
        }

        $leads->each(function (Lead $lead) use ($faker, $reps) {
            Communication::factory()
                ->count($faker->numberBetween(1, 5))
                ->forLead($lead)
                ->create(['user_id' => $reps->random()->id]);

            Task::factory()
                ->count($faker->numberBetween(0, 3))
                ->forLead($lead)
                ->create(['assignee_id' => $lead->owner_id, 'creator_id' => $lead->owner_id]);
        });

        $leads->take(18)->each(function (Lead $lead) {
            Customer::firstOrCreate(
                ['lead_id' => $lead->id],
                ['name' => $lead->name, 'company' => $lead->company, 'email' => $lead->email, 'phone' => $lead->phone]
            );
        });

        $reps->each(function (User $rep) use ($faker) {
            Target::firstOrCreate(
                [
                    'user_id' => $rep->id,
                    'period' => TargetPeriod::Monthly,
                    'period_start' => now()->startOfMonth(),
                    'period_end' => now()->endOfMonth(),
                ],
                [
                    'team_id' => null,
                    'type' => TargetType::Revenue,
                    'amount' => $faker->numberBetween(50000, 200000),
                    'created_by' => $rep->id,
                ]
            );
        });
    }
}
