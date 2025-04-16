<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $projects = Project::withCount(['companies', 'companies as plants_count' => function ($query) {
            $query->join('plants', 'companies.id', '=', 'plants.company_id');
        }, 'companies as devices_count' => function ($query) {
            $query->join('plants', 'companies.id', '=', 'plants.company_id')
                ->join('devices', 'plants.id', '=', 'devices.plant_id');
        }])->where('user_id', Auth::id())->latest()->get();

        return view('projects.index', compact('projects'));
    }

    public function show($id)
    {
        $project = Project::with([
            'companies.plants.devices',
            'companies.plants',
        ])
        ->withCount([
            'companies',
            'companies as plants_count' => function ($query) {
                $query->join('plants', 'companies.id', '=', 'plants.company_id');
            },
            'companies as devices_count' => function ($query) {
                $query->join('plants', 'companies.id', '=', 'plants.company_id')
                    ->join('devices', 'plants.id', '=', 'devices.plant_id');
            }
        ])
        ->findOrFail($id);

        return view('projects.show', compact('project'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'companies' => 'nullable|array',
            'companies.*.name' => 'nullable|string|max:255',
            'companies.*.plants' => 'nullable|array',
            'companies.*.plants.*.name' => 'nullable|string|max:255',
            'companies.*.plants.*.devices' => 'nullable|array',
            'companies.*.plants.*.devices.*.name' => 'nullable|string|max:255',
        ]);

        $user = $request->user(); // OR auth()->user()

        $project = $user->projects()->create($validated);

        foreach ($request->companies ?? [] as $companyData) {
            $company = $project->companies()->create([
                'name' => $companyData['name'] ?? null,
            ]);

            foreach ($companyData['plants'] ?? [] as $plantData) {
                $plant = $company->plants()->create([
                    'name' => $plantData['name'] ?? null,
                ]);

                foreach ($plantData['devices'] ?? [] as $deviceData) {
                    $plant->devices()->create([
                        'name' => $deviceData['name'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('dashboard')->with('message', 'Project created successfully!');
    }


    public function edit(Project $project)
    {
        $this->authorize('update', $project); // Optional: add policies if needed
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project); // Optional

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
        ]);

        $project->update($validated);

        return redirect()->route('dashboard')->with('message', 'Project updated successfully!');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project); // Optional: add policies if needed
        $project->delete();

        return redirect()->route('dashboard')->with('message', 'Project deleted successfully!');
    }
}
