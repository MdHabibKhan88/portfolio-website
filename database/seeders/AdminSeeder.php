<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /** Creates the first admin ONLY when ADMIN_EMAIL and ADMIN_PASSWORD are set in .env. No default password exists. */
    public function run(): void
    {
        $email = config('portfolio.admin_email');
        $password = config('portfolio.admin_password');

        if (blank($email) || blank($password)) {
            $this->command?->warn('No ADMIN_EMAIL / ADMIN_PASSWORD set. Run "php artisan portfolio:create-admin" to create the admin.');

            return;
        }

        if (strlen($password) < 10) {
            $this->command?->error('ADMIN_PASSWORD must be at least 10 characters. Admin not created.');

            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            ['name' => config('portfolio.admin_name'), 'password' => $password, 'is_admin' => true]
        );
    }
}
