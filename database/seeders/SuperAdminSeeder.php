<?php

namespace Database\Seeders;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SUPER_ADMIN_EMAIL', 'superadmin@example.com');
        $pass = env('SUPER_ADMIN_PASSWORD') ?: Str::random(24);

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($pass),
                'status' => UserStatus::Active->value ?? 'active',
                'email_verified_at' => now(),
            ],
        );

        $user->assignRole('super-admin');

        $this->command->info("Super Admin: {$email}");
        if (env('SEED_SUPER_ADMIN_SHOW_PASSWORD')) {
            $this->command->info("Password: {$pass}");
        }
    }
}
