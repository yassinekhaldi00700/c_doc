<?php

namespace App\Http\Controllers\Candidate;

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
        return view('candidate.dashboard', [
            'overview' => $this->stats->candidateOverview($request->user()),
        ]);
    }
}
