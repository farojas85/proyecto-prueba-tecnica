<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthService
{
    public function authenticate(string $email, string $password)
    {
        $user = DB::table('users')->where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return false;
        }

        $token = Str::random(60);
        DB::table('users')->where('id', $user->id)->update(['api_token' => $token]);

        return [
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];
    }

    public function logout(int $userId)
    {
        return DB::table('users')->where('id', $userId)->update(['api_token' => null]);
    }

    public function getAuthenticatedUser(int $userId)
    {
        return DB::table('users')->where('id', $userId)->first();
    }
}
