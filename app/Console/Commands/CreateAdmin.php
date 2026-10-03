<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create
        {--name= : Full name (prompted if omitted)}
        {--email= : Email address (prompted if omitted)}
        {--password= : Password (prompted, hidden, if omitted; avoid passing it here as it lands in shell history)}';

    protected $description = 'Create an admin user who can sign in to /admin';

    public function handle(): int
    {
        $name = $this->option('name') ?: text(
            label: 'Name',
            required: true,
            validate: fn (string $value) => $this->firstError(['name' => $value], ['name' => ['required', 'string', 'max:255']]),
        );

        $email = $this->option('email') ?: text(
            label: 'Email',
            required: true,
            validate: fn (string $value) => $this->firstError(['email' => $value], ['email' => ['required', 'email:rfc', 'max:255', 'unique:users,email']]),
        );

        $password = $this->option('password') ?: password(
            label: 'Password (min 8 characters)',
            required: true,
            validate: fn (string $value) => $this->firstError(['password' => $value], ['password' => ['required', 'string', 'min:8']]),
        );

        if (! $this->option('password')) {
            $confirmation = password(label: 'Confirm password', required: true);

            if ($confirmation !== $password) {
                $this->error('The passwords do not match. No user was created.');

                return self::FAILURE;
            }
        }

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $password],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email:rfc', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
            ]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }

            return self::FAILURE;
        }

        $user = User::create(['name' => $name, 'email' => $email, 'password' => $password]);

        // Admin pages require a verified email, and there is no signup flow to verify through.
        $user->forceFill(['email_verified_at' => now()])->save();

        $this->info("Admin created: {$user->name} <{$user->email}>. They can sign in at ".url('/login').'.');

        return self::SUCCESS;
    }

    private function firstError(array $data, array $rules): ?string
    {
        $validator = Validator::make($data, $rules);

        return $validator->fails() ? $validator->errors()->first() : null;
    }
}
