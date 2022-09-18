<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CreateClientRequest;
use App\Http\Requests\Client\TopUpClientAccountRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Resources\Client\ClientListResource;
use App\Http\Resources\Client\SingleClientResource;
use App\Http\Resources\Client\TopUpAccountRequest;
use App\Http\Services\ClientService;
use App\Http\Services\TransactionService;
use App\Models\Client;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ClientController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection {
        $clients = Client::query()
            ->with(['registrar', 'club', 'pass', 'active_session.trinket'])
            ->get();
        return ClientListResource::collection($clients);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateClientRequest $request
     * @param ClientService $clientService
     * @return JsonResponse
     */
    public function store(CreateClientRequest $request, ClientService $clientService): JsonResponse {
        $validatedData = $request->validated();
        $client = $clientService->createClient($validatedData);
        $client = SingleClientResource::make($client);
        return $this->respondSuccess(['client' => $client], 'Клиент создан успешно!');
    }

    /**
     * Display the specified resource.
     *
     * @param Client $client
     * @return SingleClientResource
     */
    public function show(Client $client): SingleClientResource {
        $client->load('pass');
        $client->load('registrar');
        $client->load('club');
        $client->load('programs.salable.service');
        $client->load('solarium.salable.service');
        $client->load('programs.salable.visits.trainer');
        $client->load('active_session');
        return SingleClientResource::make($client);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateClientRequest $request
     * @param Client $client
     * @param ClientService $clientService
     * @return JsonResponse
     */
    public function update(UpdateClientRequest $request, Client $client, ClientService $clientService): JsonResponse {
        $validatedData = $request->validated();
        $client = $clientService->updateClient($client, $validatedData);
        $client = SingleClientResource::make($client);
        return $this->respondSuccess(['client' => $client], 'Данные о клиенте успешно обновлены!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function topUpClientAccount(TopUpClientAccountRequest $request, Client $client): JsonResponse {
        TransactionService::create($client, $request->validated());
        return $this->respondSuccess(['data' => SingleClientResource::make($client)], 'Баланс клиента успешно пополнен');
    }
}
