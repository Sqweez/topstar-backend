<?php

namespace App\Actions\Dashboard;

use App\Models\Client;
use App\Models\User;

class GetSleepingClientsAction {

    public function handle() {
        /* @var User $user */
        $user = auth()->user();
        $clients = Client::query()
            // для детских клубов
            ->when(
                (!$user->getIsBossAttribute() && $user->club_id !== __hardcoded(3)),
                function ($query) use ($user) {
                    return $query->where('club_id', $user->club_id);
                })
            // для детских клубов
            ->when(
                (!$user->getIsBossAttribute() && $user->club_id === __hardcoded(3)),
                function ($query) use ($user) {
                    return $query->whereDate('birth_date', '>=', now()->subYears(14));
                })
            ->whereDoesntHave('sessions', function ($query) {
                return $query->whereDate('created_at', '>=', today()->subDays(45));
            })
            ->whereHas('sessions', function ($query) {
                return $query->whereDate('created_at', '>=', today()->subDays(120));
            })
            ->with('club')
            ->with('last_session')
            ->get();

        return $clients->map(function (Client $client) {
            return [
                'id' => $client->id,
                'name' => $client->name,
                'phone' => $client->phone,
                'club' => [
                    'id' => $client->club_id,
                    'name' => $client->club->name
                ],
                'la' => $client->last_session,
                'last_session_date' => $client->last_session ? format_date($client->last_session->created_at) : 'Неизвестно'
            ];
        });
    }
}
