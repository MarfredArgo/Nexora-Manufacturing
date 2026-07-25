<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EcommerceAdminUserProvider extends EloquentUserProvider
{
    public function retrieveByCredentials(#[\SensitiveParameter] array $credentials): ?Authenticatable
    {
        $login = $credentials['email'] ?? $credentials['username'] ?? null;

        if (! is_string($login) || $login === '') {
            return null;
        }

        return $this->newModelQuery()
            ->where(function ($query) use ($login): void {
                $query->where('company_email', $login)->orWhere('email', $login);
            })
            ->whereRaw('LOWER(COALESCE(approval_status, ?)) = ?', ['inactive', 'active'])
            ->where(function ($query): void {
                $query->whereNull('must_change_password')->orWhere('must_change_password', false);
            })
            ->whereIn(DB::raw('LOWER(department)'), [
                'e-commerce', 'ecommerce', 'electronic commerce', 'crm',
            ])
            ->first();
    }

    public function validateCredentials(Authenticatable $user, #[\SensitiveParameter] array $credentials): bool
    {
        $password = $credentials['password'] ?? null;
        $storedPassword = $user->getAuthPassword();

        if (! is_string($password) || $storedPassword === '') {
            return false;
        }

        return str_starts_with($storedPassword, '$')
            ? Hash::check($password, $storedPassword)
            : hash_equals($storedPassword, $password);
    }
}
