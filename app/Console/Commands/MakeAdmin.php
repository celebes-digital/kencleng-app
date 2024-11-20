<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

class MakeAdmin extends Command implements PromptsForMissingInput
{
    protected $signature = 'make:admin {email} {--P|password=} {--N|nama=}';

    protected $description = 'Command to create admin user';

    protected function promptForMissingArgumentsUsing()
    {
        return [
            'email' => 'Enter your email to submit as admin'
        ];
    }

    public function handle()
    {
        if (!$this->argument('email')) {
            $this->error('Email is required.');
        }

        if(!$this->option('password')) {
            $password = $this->secret('Enter your password');
        }

        if (User::where('email', $this->argument('email'))->exists()) {
            $this->newLine();
            $this->error('User already exists.');
            return;
        }

        $this->info('Create user as admin...');

        $password = $this->option('password') ?? $password;
        $user = User::create([
            'email'     => $this->argument('email'),
            'password'  => bcrypt($password),
            'is_admin'  => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $user->admin->create([
            'nama' => $this->option('nama') ?? 'Admin',
            'level' => 'superadmin',
        ]);

        $this->newLine();
        $this->info('Success to create admin user.');
    }
}
