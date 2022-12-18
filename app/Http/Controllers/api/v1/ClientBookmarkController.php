<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientBookmark;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ClientBookmarkController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return Collection
     */
    public function index(): Collection {
        return ClientBookmark::query()
            ->where('user_id', auth()->id())
            ->with('client')
            ->get()
            ->pluck('client');
    }

    public function deleteBookmark($id): JsonResponse {
        ClientBookmark::query()
            ->where('client_id', $id)
            ->where('user_id', auth()->id())
            ->delete();
        return $this->respondSuccess([], 'Клиент удален из закладок');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Collection
     */
    public function store(Request $request): Collection {
        ClientBookmark::query()
            ->updateOrCreate([
                'client_id' => $request->get('client_id'),
                'user_id' => auth()->id()
            ], []);


        return $this->index();
    }
}
