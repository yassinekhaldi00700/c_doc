<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', ['settings' => SiteSettings::all()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'application_deadline' => ['required', 'date_format:Y-m-d'],
            'oral_exam_min_date' => ['required', 'date_format:Y-m-d'],
            'oral_exam_max_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:oral_exam_min_date'],
        ], [
            'application_deadline.required' => 'La date limite est obligatoire.',
            'application_deadline.date_format' => 'La date limite n’est pas valide.',
            'oral_exam_min_date.required' => 'La date de début des examens oraux est obligatoire.',
            'oral_exam_min_date.date_format' => 'La date de début des examens oraux n’est pas valide.',
            'oral_exam_max_date.required' => 'La date de fin des examens oraux est obligatoire.',
            'oral_exam_max_date.date_format' => 'La date de fin des examens oraux n’est pas valide.',
            'oral_exam_max_date.after_or_equal' => 'La date de fin des examens oraux doit être égale ou postérieure à la date de début.',
        ]);

        SiteSettings::update($validated);

        return redirect()->route('admin.settings.edit')->with('success', 'Dates updated.');
    }
}
