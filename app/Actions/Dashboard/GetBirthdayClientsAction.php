<?php

namespace App\Actions\Dashboard;

use App\Http\Resources\Client\BirthdayClientListResource;
use App\Models\Client;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetBirthdayClientsAction {

    public function handle(): AnonymousResourceCollection {
        $user = auth()->user();
        $clients = Client::query()
            ->when(!$user->is_boss, function ($query) use ($user) {
                return $query->where('club_id', $user->club_id);
            })
            ->whereDay('birth_date', today())
            ->whereMonth('birth_date', today())
            ->select(['id', 'name', 'phone', 'birth_date'])
            ->get();

        return BirthdayClientListResource::collection($clients);
    }
}
