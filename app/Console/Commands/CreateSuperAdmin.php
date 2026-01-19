<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:super-admin 
                            {email : The email address of the super admin}
                            {--force : Override existing super admin (requires current password)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create or update a super admin user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $email = $this->argument('email');
        $force = $this->option('force');

        // Validate email
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            $this->error('Invalid email address.');
            return Command::FAILURE;
        }

        // Check if super admin exists
        $existingSuperAdmin = User::where('role', 'super_admin')->first();

        if ($existingSuperAdmin && !$force) {
            $this->error('A super admin already exists. Use --force to override.');
            return Command::FAILURE;
        }

        // If force flag is used and super admin exists, verify password
        if ($existingSuperAdmin && $force) {
            $currentPassword = $this->secret('Enter current super admin password:');
            
            if (!Hash::check($currentPassword, $existingSuperAdmin->password)) {
                $this->error('Incorrect password. Aborting.');
                return Command::FAILURE;
            }

            $this->info('Password verified. Proceeding with update...');
        }

        // Get or create user
        $user = User::where('email', $email)->first();

        if (!$user) {
            // Create new user
            $name = $this->ask('Enter name for the super admin', $email);
            $password = $this->secret('Enter password for the super admin:');
            $passwordConfirmation = $this->secret('Confirm password:');

            if ($password !== $passwordConfirmation) {
                $this->error('Passwords do not match.');
                return Command::FAILURE;
            }

            if (strlen($password) < 8) {
                $this->error('Password must be at least 8 characters.');
                return Command::FAILURE;
            }

            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'super_admin',
            ]);

            $this->info("Super admin created successfully: {$user->email}");
        } else {
            // Update existing user
            if ($user->role === 'super_admin' && $user->id !== $existingSuperAdmin->id) {
                $this->warn("User {$user->email} already exists. Updating to super admin...");
            }

            $updatePassword = $this->confirm('Do you want to update the password?', false);

            if ($updatePassword) {
                $password = $this->secret('Enter new password:');
                $passwordConfirmation = $this->secret('Confirm password:');

                if ($password !== $passwordConfirmation) {
                    $this->error('Passwords do not match.');
                    return Command::FAILURE;
                }

                if (strlen($password) < 8) {
                    $this->error('Password must be at least 8 characters.');
                    return Command::FAILURE;
                }

                $user->password = Hash::make($password);
            }

            $user->role = 'super_admin';
            $user->save();

            $this->info("Super admin updated successfully: {$user->email}");
        }

        return Command::SUCCESS;
    }
}


