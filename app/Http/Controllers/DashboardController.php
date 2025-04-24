<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    // public function index()
    // {
    //     $projects = Project::withCount([
    //         'companies',
    //         'companies as plants_count' => fn($q) =>
    //             $q->join('plants', 'companies.id', '=', 'plants.company_id'),
    //         'companies as devices_count' => fn($q) =>
    //             $q->join('plants', 'companies.id', '=', 'plants.company_id')
    //             ->join('devices', 'plants.id', '=', 'devices.plant_id'),
    //     ])->latest()->get();

    //     return view('dashboard.index', compact('projects'));
    // }


    public function index()
    {
        $plants = Plant::with('mainFeeds.devices')->paginate();
        return view('plants.index', compact('plants'));
    }

    public function show($id)
    {
        $project = Project::with([
            'companies.plants.devices',
            'companies.plants',
        ])->findOrFail($id);

        return view('dashboard.show', compact('project'));


}

}
