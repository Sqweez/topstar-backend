<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Service\CreateServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Http\Resources\Client\ClientPurchasedServices;
use App\Http\Resources\Service\ServicesListResource;
use App\Http\Resources\Service\SingleServiceResource;
use App\Http\Services\ServiceService;
use App\Models\Service;
use App\Models\ServiceSale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ServiceController extends ApiController
{
    public function getServiceTypes(): array {
        return Service::TYPES;
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        $services = Service::query()
            ->with('club')
            ->get();
        return ServicesListResource::collection($services);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateServiceRequest $request
     * @return JsonResponse
     */
    public function store(CreateServiceRequest $request, ServiceService $serviceService): JsonResponse {
        $validatedData = $request->validated();
        $service = $serviceService->createService($validatedData);
        $service = ServicesListResource::make($service);
        return $this->respondSuccess(['service' => $service], 'Услуга успешно создана!');
    }

    /**
     * Display the specified resource.
     *
     * @param Service $service
     * @return SingleServiceResource
     */
    public function show(Service $service): SingleServiceResource {
        return SingleServiceResource::make($service);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateServiceRequest $request
     * @param Service $service
     * @param ServiceService $serviceService
     * @return JsonResponse
     */
    public function update(UpdateServiceRequest $request, Service $service, ServiceService $serviceService): JsonResponse {
        $validatedData = $request->validated();
        $service = $serviceService->updateService($service, $validatedData);
        $service = ServicesListResource::make($service);
        return $this->respondSuccess(['service' => $service], 'Услуга успешно обновлена!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        Service::whereKey($id)->delete();
        return $this->respondSuccess([], 'Услуга успешно удалена!');
    }

    /*
     * Активирует купленную ранее тренировку
     * */
    public function activateService(Request $request, ServiceSale $service, ServiceService $serviceService): JsonResponse {
        $sale = $serviceService->activateService($service);
        return $this->respondSuccess(
            ['program' => ClientPurchasedServices::make($sale)],
            'Программа успешно активирована!'
        );
    }
}
