<?php

namespace App\Http\Controllers\api\v1;

use App\Actions\Stats\GetActiveClientsAction;
use App\Actions\Stats\GetClientsByClubAction;
use App\Http\Controllers\Controller;
use App\Models\Club;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsController extends ApiController
{
    public function getClientsByClub(GetClientsByClubAction $action): JsonResponse {
        return $this->respondSuccessNoReport($action->handle());
    }

    public function getActiveClients(GetActiveClientsAction $action): JsonResponse {
        return $this->respondSuccessNoReport($action->handle());
    }
}
