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
        return view('admin.settings.edit', ['deadline' => SiteSettings::deadline()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'application_deadline' => ['required', 'date_format:Y-m-d'],
        ], [
            'application_deadline.required' => 'La date limite est obligatoire.',
            'application_deadline.date_format' => 'La date limite n’est pas valide.',
        ]);

        SiteSettings::update($validated);

        return redirect()->route('admin.settings.edit')->with('success', 'Application deadline updated.');
    }
}
