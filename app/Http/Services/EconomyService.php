<?php

namespace App\Http\Services;

use App\Http\Resources\Economy\AccountTopUp;
use App\Http\Resources\Economy\ClubGuests;
use App\Http\Resources\Economy\NewClients;
use App\Http\Resources\Economy\PurchasedPrograms;
use App\Models\Client;
use App\Models\Club;
use App\Models\Sale;
use App\Models\ServiceSale;
use App\Models\Session;
use App\Models\Transaction;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EconomyService {

    public function getReports($dates) {
        $start = $dates['start'];
        $finish = $dates['finish'];
        return [
            'accounts_top_ups' => $this->getBalancesTopUpReport($start, $finish),
            'programs_purchased' => $this->getProgramPurchasedReport($start, $finish),
            'new_clients' => $this->getNewClientsReport($start, $finish),
            'club_guests' => $this->getClubGuestsReport($start, $finish),
        ];
    }

    public function getClientsBalance() {
        return
            Club::query()
                ->select(['id', 'name'])
                ->withSum('clients', 'balance')
                ->get()
                ->map(function ($club) {
                    $club['amount'] = $club['clients_sum_balance'] ? intval($club['clients_sum_balance']) : 0;
                    return $club;
                });
    }

    private function getBalancesTopUpReport($start, $finish): AnonymousResourceCollection {
        $transactions = Transaction::query()
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $finish)
            ->where('amount', '>', 0)
            ->whereNull('cancelled_at')
            ->with(['club:id,name', 'client:id,name', 'user:id,name'])
            ->get();
        return AccountTopUp::collection($transactions);
    }

    private function getClubGuestsReport($start, $finish): AnonymousResourceCollection {
        $sessions = Session::query()
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $finish)
            ->with(['client', 'start_user', 'finish_user', 'club'])
            ->whereNotNull('finished_at')
            ->get();
        return ClubGuests::collection($sessions);
    }

    private function getProgramPurchasedReport($start, $finish): AnonymousResourceCollection {
        $sales = Sale::query()
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $finish)
            ->where('salable_type', ServiceSale::class)
            ->with(['client', 'user', 'club', 'salable.service'])
            ->get();
        return PurchasedPrograms::collection($sales);
    }

    private function getNewClientsReport($start, $finish): AnonymousResourceCollection {
        $clients = Client::query()
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $finish)
            ->with(['club', 'registrar'])
            ->get();
        return NewClients::collection($clients);
    }
}
