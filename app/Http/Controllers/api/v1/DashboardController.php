<?php

namespace App\Http\Controllers\api\v1;

use App\Actions\Dashboard\GetInGymClientsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Client\ClientListResource;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getInGymClients(GetInGymClientsAction $action) {
        $clients = $action->handle();
        return ClientListResource::collection($clients);
    }
}
