<?php

namespace App\Http\Services;

use App\Actions\Sale\ServiceSaleAction;
use App\Models\Client;
use App\Models\Sale;
use App\Models\Service;
use App\Models\ServiceSale;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SaleService {

    private $serviceSaleAction;
    private $productSaleAction;

    public function __construct(ServiceSaleAction $serviceSaleAction) {
        $this->serviceSaleAction = $serviceSaleAction;
    }

    public function create( array $payload = []) {
        if ($payload['type'] === Sale::TYPE_SERVICE) {
            return $this->serviceSaleAction->handle($payload);
        }
    }
}
