<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use Illuminate\View\View;

class DashboardController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display the dashboard with a list of plants.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $plants = Plant::with('controllers.mainFeeds.devices')->paginate();
        return view('plants.index', compact('plants'));
    }

}
