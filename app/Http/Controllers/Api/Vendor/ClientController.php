<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ArchivedClient;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $vendor = $request->user();
        $clientsFromRequests = User::whereIn(
            'id',
            $vendor->vendorRequests()->select('client_id')
        )->get();

        $registeredClients = $vendor->registeredClients()->get();
        $archivedIds = ArchivedClient::where('vendor_id', $vendor->id)->pluck('client_id')->filter()->all();

        return response()->json([
            'clients' => $clientsFromRequests
                ->merge($registeredClients)
                ->unique('id')
                ->whereNotIn('id', $archivedIds)
                ->values(),
            'archived_clients' => ArchivedClient::where('vendor_id', $vendor->id)->latest()->get(),
        ]);
    }

    public function archive(Request $request, User $client): JsonResponse
    {
        $vendor = $request->user();

        $archived = ArchivedClient::updateOrCreate(
            ['vendor_id' => $vendor->id, 'client_id' => $client->id],
            [
                'client_name' => $client->name,
                'client_email' => $client->email,
            ]
        );

        return response()->json(['archived_client' => $archived]);
    }
}
