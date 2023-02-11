<?php

namespace App\Repositories\Client;

use App\Models\Client;

class RetrieveSingleClient {

    public static function retrieve(Client $client): Client {
        $client->load('pass');
        $client->load('registrar');
        $client->load('club');
        $client->load('lastPrograms.salable.service');
        $client->load('lastPrograms.salable.restores');
        $client->load('lastPrograms.salable.visits.trainer');
        $client->load('lastPrograms.club');
        $client->load('solarium.salable.service');
        $client->load('active_session');
        return $client;
    }
}
