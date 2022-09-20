<?php

namespace App\Repositories\Client;

use App\Models\Client;

class RetrieveSingleClient {

    public static function retrieve(Client $client): Client {
        $client->load('pass');
        $client->load('registrar');
        $client->load('club');
        $client->load('programs.salable.service');
        $client->load('programs.salable.restores');
        $client->load('solarium.salable.service');
        $client->load('programs.salable.visits.trainer');
        $client->load('active_session');
        return $client;
    }
}
