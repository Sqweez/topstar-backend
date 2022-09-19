<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserService {

    /**
     * @throws Throwable
     */
    public function createUser($payload = []): ?User {
        return DB::transaction(function () use ($payload) {
            $user = User::create(Arr::except($payload, ['pass']));
            if (isset($payload['pass'])) {
                $pass = PassService::createPass($payload['pass']);
                $user->pass()->save($pass);
            }
            $user->roles()->sync($payload['roles']);
            return $user;
        });
    }

    public function updateUser(User $user, $payload = []): User {
        return DB::transaction(function () use ($payload, $user) {
            $user->update($payload);
            if (isset($payload['pass'])) {
                $user->pass()->delete();
                $pass = PassService::createPass($payload['pass']);
                $user->pass()->save($pass);
            }
            $user->roles()->sync($payload['roles']);
            return $user;
        });
    }

    public function deleteUser(User $user) {
        $user->pass()->delete();
        $user->delete();
    }
}
