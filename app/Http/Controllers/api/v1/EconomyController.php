<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Economy\GetReportsRequest;
use App\Http\Services\EconomyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EconomyController extends ApiController
{
    public function index(GetReportsRequest $request, EconomyService $service) {
        $dates = $request->validated();
        $reports = $service->getReports($dates);
        return $this->respondSuccessNoReport([
            'reports' => $reports
        ]);
    }

    public function getClientsBalance(EconomyService $service): JsonResponse {
        return $this->respondSuccessNoReport([
            'reports' => $service->getClientsBalance()
        ]);
    }

    public function getGraphReports(GetReportsRequest $request, EconomyService $service): JsonResponse {
        return $this->respondSuccessNoReport([
            'reports' => $service->getGraphReports($request->validated())
        ]);
    }
}
