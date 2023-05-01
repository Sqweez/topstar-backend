<?php

namespace App\Actions\Dashboard;

use App\Models\Client;
use App\Models\ServiceSale;
use App\Models\User;
use Carbon\Carbon;

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
            ->whereHas('programs', function ($q) {
                return $q
                    ->whereHasMorph('salable', [ServiceSale::class], function ($q) {
                        return $q->whereDate('active_until', today()->subDays(45));
                    });
            })
           /* ->whereDoesntHave('programs', function ($q) {
                return $q
                    ->whereHasMorph('salable', [ServiceSale::class], function ($q) {
                        return $q
                            ->whereDate('active_until', '>', today()->subDays(45))
                            ->whereNotNull('active_until');
                    });
            })*/
            ->with('programs.salable')
            ->with('club')
            ->with('last_session')
            ->get();

        return $clients->filter(function (Client $client) {
            $activePrograms = $client->programs->filter(function ($program) {
                return ($program->salable->active_until === null ||
                    Carbon::parse($program->salable->active_until)->gt(today()->subDays(45))) &&
                    !in_array($program->salable->service_id, [176, 148]);
            });
            return $activePrograms->count() === 0;
        })
            ->values()
            ->map(function (Client $client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'phone' => $client->phone,
                    'club' => [
                        'id' => $client->club_id,
                        'name' => $client->club->name
                    ],
                    'la' => $client->last_session,
                    'last_session_date' => $client->last_session ? format_date($client->last_session->created_at) : 'Неизвестно',
                    'pgorams' => $client->programs,
            ];
        });
    }
}
