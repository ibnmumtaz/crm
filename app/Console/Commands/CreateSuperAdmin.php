<?php

namespace App\Console\Commands;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateSuperAdmin extends Command
{
    protected $signature = 'crm:create-super-admin {--email= : Super admin email} {--password= : Super admin password}';

    protected $description = 'Create a Super Admin user';

    public function handle(): int
    {
        $email = $this->option('email') ?: env('SUPER_ADMIN_EMAIL', 'superadmin@example.com');
        $pass = $this->option('password') ?: Str::random(24);

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Super Admin',
                'password' => Hash::make($pass),
                'status' => UserStatus::Active,
                'email_verified_at' => now(),
            ],
        );

        $user->assignRole('super-admin');

        $this->info("Super Admin created: {$email}");
        $this->info("Password: {$pass}");

        return self::SUCCESS;
    }
}
