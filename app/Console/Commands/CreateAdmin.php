<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdmin extends Command
{
    protected $signature = 'portfolio:create-admin {--name= : Admin name} {--email= : Admin email}';

    protected $description = 'Create (or reset the password of) the portfolio administrator account';

    public function handle(): int
    {
        $name = $this->option('name') ?: config('portfolio.admin_name') ?: 'Administrator';
        $email = $this->option('email') ?: config('portfolio.admin_email') ?: $this->ask('Admin email address');
        $password = config('portfolio.admin_password') ?: $this->secret('Admin password (minimum 10 characters, letters and numbers)');

        if (! config('portfolio.admin_password')) {
            $confirm = $this->secret('Confirm password');
            if ($confirm !== $password) {
                $this->error('The passwords do not match.');

                return self::FAILURE;
            }
        }

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password],
            [
                'name' => ['required', 'string', 'max:100'],
                'email' => ['required', 'email', 'max:150'],
                'password' => ['required', Password::min(10)->letters()->numbers()],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        $user = User::where('email', $email)->first();

        if ($user) {
            $user->forceFill(['name' => $name, 'password' => $password, 'is_admin' => true])->save();
            $this->info("Existing account updated: {$email}");
        } else {
            User::create(['name' => $name, 'email' => $email, 'password' => $password, 'is_admin' => true]);
            $this->info("Admin account created: {$email}");
        }

        return self::SUCCESS;
    }
}
