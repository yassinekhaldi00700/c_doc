<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\AdmissionSetting;
use App\Models\ResearchSubject;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $subjects = ResearchSubject::open()
            ->with(['professor', 'department'])
            ->when($request->filled('department_id'), fn ($query) => $query->where('department_id', $request->department_id))
            ->when($request->filled('search'), fn ($query) => $query->where('title', 'like', '%'.$request->search.'%'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('welcome', [
            'subjects' => $subjects,
            'departments' => Department::orderBy('name')->get(),
            'totalOpenSubjects' => ResearchSubject::open()->count(),
            'applicationsPaused' => AdmissionSetting::applicationsArePaused(),
        ]);
    }

    public function about(): View
    {
        return view('about');
    }
}
