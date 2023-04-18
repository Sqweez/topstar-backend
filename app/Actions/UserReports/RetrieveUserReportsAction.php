<?php

namespace App\Actions\UserReports;

use App\Http\Resources\Economy\PurchasedPrograms;
use App\Http\Resources\UserReports\ReplenishmentsResource;
use App\Http\Resources\UserReports\WithdrawalResource;
use App\Models\ClientReplenishment;
use App\Models\Sale;
use App\Models\ServiceSale;
use App\Models\User;
use App\Models\WithDrawal;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RetrieveUserReportsAction {

    private $start;
    private $finish;
    private User $user;

    public function handle(User $user, $start = null, $finish = null) {
        $this->user = $user;
        $this->start = $start;
        $this->finish = $finish;

        return [
            'replenishments' => $this->getReplenishments(),
            'withdrawals' => $this->getWithdrawals(),
            'service_sales' => $this->getServiceSales(),
        ];
    }

    private function getReplenishments(): AnonymousResourceCollection {
        $replenishments = ClientReplenishment::query()
            ->where('user_id', $this->user->id)
            ->when(($this->start && $this->finish), function ($query) {
                return $query
                    ->whereDate('created_at', '>=', $this->start)
                    ->whereDate('created_at', '<=', $this->finish);
            })
            ->with([
                'user:id,name',
                'club:id,name',
                'client:id,name',
            ])
            ->get();

        return ReplenishmentsResource::collection($replenishments);
    }

    private function getWithdrawals(): AnonymousResourceCollection {
        $items = WithDrawal::query()
            ->where('user_id', $this->user->id)
            ->when(($this->start && $this->finish), function ($query) {
                return $query
                    ->whereDate('created_at', '>=', $this->start)
                    ->whereDate('created_at', '<=', $this->finish);
            })
            ->with([
                'user:id,name',
                'club:id,name',
            ])
            ->get();

        return WithdrawalResource::collection($items);
    }

    private function getServiceSales(): AnonymousResourceCollection {
        $items = Sale::query()
            ->where('user_id', $this->user->id)
            ->whereDate('created_at', '>=', $this->start)
            ->whereDate('created_at', '<=', $this->finish)
            ->where('salable_type', ServiceSale::class)
            ->with([
                'client:id,name',
                'user:id,name',
                'club:id,name',
                'salable.service',
                'transaction'
            ])
            ->get();

        return PurchasedPrograms::collection($items);
    }
}
