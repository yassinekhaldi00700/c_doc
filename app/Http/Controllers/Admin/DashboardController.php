<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardStatsService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(protected DashboardStatsService $stats)
    {
    }

    public function index(): View
    {
        return view('admin.dashboard', [
            'overview' => $this->stats->adminOverview(),
        ]);
    }
}
