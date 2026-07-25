<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class InstallItsmSchema extends Command
{
    protected $signature = 'itsm:install-schema';

    protected $description = 'Install the ITSM schema and create the initial Nexora root administrator';

    public function handle(): int
    {
        $exitCode = $this->call('migrate', [
            '--database' => 'pgsql',
            '--path' => 'database/migrations',
            '--force' => true,
        ]);

        if ($exitCode !== self::SUCCESS) {
            return $exitCode;
        }

        $username = env('ROOT_ADMIN_USERNAME', 'root');
        $email = env('ROOT_ADMIN_EMAIL', 'root@nexora.mail');
        $password = env('ROOT_ADMIN_PASSWORD', 'Nexora123!');

        $root = User::query()->firstOrCreate(
            ['username' => $username],
            [
                'name' => 'Nexora Root Administrator',
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'root_admin',
                'status' => 'active',
            ],
        );

        if ($root->wasRecentlyCreated) {
            $this->warn("Created root administrator: {$username}");
        }

        return self::SUCCESS;
    }
}
