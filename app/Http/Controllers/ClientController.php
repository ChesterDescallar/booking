<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::withCount('bookings')
            ->orderBy('name')
            ->get();

        return ClientResource::collection($clients);
    }

    public function store(StoreClientRequest $request)
    {
        $client = Client::create([
            'name' => $request->validated()['name'],
            'email' => $request->validated()['email'],
            'phone' => $request->validated()['phone'],
        ]);

        return (new ClientResource($client))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Client $client)
    {
        $client->loadCount('bookings');
        return new ClientResource($client);
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $client->update([
            'name' => $request->validated()['name'],
            'email' => $request->validated()['email'],
            'phone' => $request->validated()['phone'],
        ]);

        return new ClientResource($client);
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json(null, 204);
    }
}
