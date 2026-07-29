<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Services\DashboardStatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected DashboardStatsService $stats)
    {
    }

    public function index(Request $request): View
    {
        return view('professor.dashboard', [
            'overview' => $this->stats->professorOverview($request->user()),
        ]);
    }
}
