<?php

namespace App\Actions\Sale;

use App\Http\Services\TransactionService;
use App\Models\Client;
use App\Models\Sale;
use App\Models\Service;
use App\Models\ServiceSale;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ServiceSaleAction {

    public function handle(array $payload) {
        return DB::transaction(function () use ($payload) {
            $service = Service::find($payload['service_id']);
            $client = Client::find($payload['client_id']);
            $repeatCount = $payload['count'];
            for ($i = 0; $i < $repeatCount; $i++) {
                $transactionPayload = $this->_mapTransactionPayload($service, $payload);
                $transaction = TransactionService::create($client, $transactionPayload);
                $serviceSalePayload = $this->_mapServiceSalePayload($service, $payload);
                $serviceSale = ServiceSale::create($serviceSalePayload);
                $salePayload = $this->_mapSalePayload($transaction, $serviceSale, $payload);
                Sale::create($salePayload);
            }
            return $client;
        });
    }

    public function _mapServiceSalePayload(Service $service, $payload): array {
        return [
            'service_id' => $service->id,
            'entries_count' => $service->entries_count ?? null,
            'minutes_remaining' => ($service->validity_minutes && $service->validity_minutes > 0) ? $service->validity_minutes : null,
            'active_until' => null,
            'user_id' => $payload['user_id']
        ];
    }

    public function _mapTransactionPayload(Service $service, $payload): array {

        $serviceName = $service->name;

        if ($payload['is_prolongation']) {
            $serviceName = "пролонгацию " . $serviceName;
        }

        $description = sprintf("Списание средств за %s", $serviceName);

        return [
            'user_id' => $payload['user_id'],
            'client_id' => $payload['client_id'],
            'amount' => $payload['amount'] * -1,
            'club_id' => $payload['club_id'],
            'payment_type' => __hardcoded(3),
            'description' => $description,
        ];
    }

    public function _mapSalePayload(Transaction $transaction, ServiceSale $serviceSale, $payload): array {
        return [
            'client_id' => $payload['client_id'],
            'user_id' => $payload['user_id'],
            'club_id' => $payload['club_id'],
            'transaction_id' => $transaction->id,
            'salable_type' => ServiceSale::class,
            'salable_id' => $serviceSale->id,
        ];
    }
}
