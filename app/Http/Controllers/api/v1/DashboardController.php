<?php

namespace App\Http\Controllers\api\v1;

use App\Actions\Dashboard\GetBirthdayClientsAction;
use App\Actions\Dashboard\GetInGymClientsAction;
use App\Actions\Dashboard\GetSleepingClientsAction;
use App\Actions\Dashboard\GetTodayGuestClientsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Client\BirthdayClientListResource;
use App\Http\Resources\Dashboard\DashboardClientListResource;
use App\Models\Client;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DashboardController extends Controller
{
    public function getInGymClients(GetInGymClientsAction $action): AnonymousResourceCollection {
        $clients = $action->handle();
        return DashboardClientListResource::collection($clients);
    }

    public function getGuestsClients(GetTodayGuestClientsAction $action): AnonymousResourceCollection {
        $clients = $action->handle();
        return DashboardClientListResource::collection($clients);
    }

    public function getBirthdayClients(GetBirthdayClientsAction $action): AnonymousResourceCollection {
        return $action->handle();
    }

    public function getSleepingClients(GetSleepingClientsAction $action) {
        return $action->handle();
    }
}
