<?php

namespace App\Actions\Dashboard;

use App\Http\Resources\Client\BirthdayClientListResource;
use App\Models\Client;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetBirthdayClientsAction {

    public function handle(): AnonymousResourceCollection {
        $clients = Client::query()
            ->whereDay('birth_date', today())
            ->whereMonth('birth_date', today())
            ->select(['id', 'name', 'phone', 'birth_date'])
            ->get();

        return BirthdayClientListResource::collection($clients);
    }
}
