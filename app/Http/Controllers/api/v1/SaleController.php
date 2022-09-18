<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\CreateSaleRequest;
use App\Http\Resources\Client\SingleClientResource;
use App\Http\Services\SaleService;

class SaleController extends ApiController
{
    public function create(CreateSaleRequest $request, SaleService $saleService) {
        $validatedData = $request->validated();
        $client = $saleService->create($validatedData);
        return $this->respondSuccess(['client' => SingleClientResource::make($client)], $this->getSuccessMessage($validatedData));
    }

    private function getSuccessMessage(array $data) {
        switch ($data) {
            case isset($data['service_id']) && empty($data['is_prolongation']):
                return __('messages.service_sale_success');
            case isset($data['is_prolongation']):
                return __('messages.service_prolong_success');
            default:
                return __('messages.default_success');
        }
    }
}
