<?php

namespace App\Http\Services;

use App\Models\Client;
use App\Models\SessionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ClientService {

    public function createClient($payload = []): ?Client {
        return DB::transaction(function () use ($payload) {
            $client = Client::create(Arr::except($payload, ['pass', 'photo']));
            if (isset($payload['pass'])) {
                $pass = PassService::createPass($payload['pass']);
                $client->pass()->save($pass);
            }
            if (isset($payload['photo'])) {
                $client->addMedia($payload['photo'])->toMediaCollection(Client::MEDIA_AVATAR);
            }
            return $client;
        });
    }

    public function updateClient(Client $client, $payload = []): ?Client {
        return DB::transaction(function () use ($client, $payload) {
            $client->update(Arr::except($payload, ['pass', 'photo']));
            if (isset($payload['pass'])) {
                $client->pass()->delete();
                $pass = PassService::createPass($payload['pass']);
                $client->pass()->save($pass);
            }
            if (isset($payload['photo'])) {
                $oldMedia = $client->getFirstMedia(Client::MEDIA_AVATAR);
                try {
                    $oldMedia->delete();
                } catch (\Exception $exception) {
                    \Log::error($exception->getMessage());
                }
                $client->addMedia($payload['photo'])->toMediaCollection(Client::MEDIA_AVATAR);
            }
            return $client;
        });
    }

    /**
     * @throws \Throwable
     */
    public function writeOff($payload = []) {
        return DB::transaction(function () use ($payload) {
            $client = Client::find($payload['client_id']);
            $session = $client->active_session;
            if (!$session) {
                $session = $this->open($client, $payload);
            }
            SessionService::create([
                'service_sale_id' => $payload['service_sale_id'],
                'user_id' => $payload['user_id'],
                'session_id' => $session->id,
                'trainer_id' => $payload['trainer_id']
            ]);
            return Client::find($client->id);
        });
    }

    public function writeOffSolarium(array $payload) {
        $client = Client::find($payload['client_id']);
        $minutes = $payload['minutes'];
        $activeSolarium = $client->solarium->sortBy('id');
        $activeSolarium->load('salable.visits');

        $session = $this->open($client, $payload);

        $activeSolarium = $activeSolarium->filter(function ($solarium) {
            return $solarium->salable->remaining_minutes > 0;
        });

        foreach ($activeSolarium as $item) {
            if ($minutes > 0) {
                $needleMinutes = min($minutes, $item->salable->remaining_minutes);
                $minutes -= $needleMinutes;
                SessionService::create([
                    'user_id' => $payload['user_id'],
                    'service_sale_id' => $item->id,
                    'session_id' => optional($session)->id,
                    'minutes' => $needleMinutes
                ]);
            }
        }

        return Client::find($client->id);
    }

    public function open(Client $client, $payload = []): Model {
        if ($client->active_session) {
            return $client->active_session;
        }
        return $client->sessions()->create([
            'start_user_id' => $payload['user_id'],
            'club_id' => auth()->user()->club_id,
            'trinket_id' => null,
        ]);
    }

    public function finish(Client $client) {
        $session = $client->active_session;
        $session->update([
            'finish_user_id' => auth()->id(),
            'finished_at' => now(),
        ]);
    }
}
