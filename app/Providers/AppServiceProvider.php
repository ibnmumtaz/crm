<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            DB::statement('PRAGMA journal_mode = WAL');
            DB::statement('PRAGMA foreign_keys = ON');
            DB::statement('PRAGMA busy_timeout = 5000');
            DB::statement('PRAGMA synchronous = NORMAL');
        } catch (\Throwable $e) {
            //
        }

        Gate::before(function (User $user) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
