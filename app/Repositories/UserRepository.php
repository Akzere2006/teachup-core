<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function __construct()
    {
        logger('UserRepository instantiated');
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update($userId, array $data): User
    {
        $user = User::findOrFail($userId);
        $user->update($data);
        return $user;
    }
}

