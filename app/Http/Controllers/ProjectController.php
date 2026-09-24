<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\Client;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'all');
        $query = Project::with(['client', 'invoices', 'expenses'])->latest('id');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $projects = $query->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'code' => $p->code,
                'title' => $p->title,
                'category' => $p->category,
                'status' => $p->status,
                'total_budget' => $p->total_budget,
                'paid_amount' => $p->paid_amount,
                'remaining_amount' => $p->remaining_amount,
                'start_date' => $p->start_date ? $p->start_date->format('Y-m-d') : null,
                'deadline' => $p->deadline ? $p->deadline->format('Y-m-d') : null,
                'progress_percent' => $p->progress_percent,
                'description' => $p->description,
                'client' => $p->client ? [
                    'id' => $p->client->id,
                    'name' => $p->client->name,
                    'company' => $p->client->company,
                    'whatsapp' => $p->client->whatsapp,
                ] : null,
            ];
        });

        $clients = Client::orderBy('name')->get(['id', 'name', 'company']);

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'clients' => $clients,
            'current_status' => $status,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'code' => 'required|string|unique:projects,code',
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'status' => 'required|in:lead,deal_dp,in_progress,review,completed,cancelled',
            'total_budget' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date',
            'progress_percent' => 'nullable|integer|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        Project::create($validated);
        return redirect()->back()->with('success', 'Proyek berhasil didaftarkan!');
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'code' => 'required|string|unique:projects,code,' . $project->id,
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'status' => 'required|in:lead,deal_dp,in_progress,review,completed,cancelled',
            'total_budget' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'deadline' => 'nullable|date',
            'progress_percent' => 'nullable|integer|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        $project->update($validated);
        return redirect()->back()->with('success', 'Data proyek berhasil diperbarui!');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->back()->with('success', 'Proyek berhasil dihapus.');
    }
}
