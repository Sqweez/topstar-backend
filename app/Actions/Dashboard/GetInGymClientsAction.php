<?php

namespace App\Actions\Dashboard;

use App\Models\Client;
use App\Models\User;

class GetInGymClientsAction {

    public function handle() {
        /* @var User $user */
        $user = auth()->user();
        return Client::query()
            ->when(!$user->is_boss, function ($query) use ($user) {
                return $query->where('club_id', $user->club_id);
            })
            ->has('active_session')
            ->with(['club', 'pass', 'active_session.trinket'])
            ->get();
    }
}
