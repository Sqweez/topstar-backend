<?php

namespace App\Http\Services;

use App\Models\Service;
use App\Models\ServiceSale;

class ServiceService {

    public function createService($payload = []): Service {
        return Service::create($payload);
    }

    public function updateService(Service $service, $payload = []): Service {
        $service->update($payload);
        return $service;
    }

    public function activateService(ServiceSale $serviceSale) {
        $service = $serviceSale->service;
        $activeUntil = now()->addDays($service->validity_days - 1);
        // Если солярий, то прибавляем 100 лет
        if ($service->service_type_id === 2) {
            $activeUntil = now()->addYears(100);
        }
        $serviceSale->update([
            'active_until' => $activeUntil
        ]);
        return $serviceSale->sale;
    }
}
