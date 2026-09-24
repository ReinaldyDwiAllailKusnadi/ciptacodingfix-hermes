<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Client;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::withCount('projects')
            ->with(['projects' => function ($q) {
                $q->latest('id')->limit(3);
            }])
            ->latest('id')
            ->get();

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'status' => 'required|in:active,prospect,inactive',
            'notes' => 'nullable|string',
        ]);

        Client::create($validated);
        return redirect()->back()->with('success', 'Data klien berhasil ditambahkan!');
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'status' => 'required|in:active,prospect,inactive',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);
        return redirect()->back()->with('success', 'Data klien berhasil diperbarui!');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->back()->with('success', 'Data klien berhasil dihapus.');
    }
}
