<?php

namespace App\Http\Controllers\api\v1;

use App\Actions\Dashboard\GetInGymClientsAction;
use App\Actions\Dashboard\GetTodayGuestClientsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Client\ClientListResource;
use App\Http\Resources\Dashboard\DashboardClientListResource;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getInGymClients(GetInGymClientsAction $action) {
        $clients = $action->handle();
        return DashboardClientListResource::collection($clients);
    }

    public function getGuestsClients(GetTodayGuestClientsAction $action) {
        $clients = $action->handle();
        return DashboardClientListResource::collection($clients);
    }
}
