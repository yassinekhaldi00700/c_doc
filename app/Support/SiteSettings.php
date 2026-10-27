<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Admin-editable site settings (currently the application deadline shown in
 * the announcement bar). Kept in storage/app/site-settings.json rather than a
 * new database column, so the schema stays unchanged.
 */
class SiteSettings
{
    private const FILE = 'site-settings.json';

    private const DEFAULTS = ['application_deadline' => '2026-10-31'];

    public static function all(): array
    {
        $disk = Storage::disk('local');
        $saved = $disk->exists(self::FILE) ? json_decode($disk->get(self::FILE), true) : [];

        return array_merge(self::DEFAULTS, is_array($saved) ? $saved : []);
    }

    public static function deadline(): Carbon
    {
        return Carbon::parse(self::all()['application_deadline']);
    }

    public static function update(array $values): void
    {
        Storage::disk('local')->put(self::FILE, json_encode(array_merge(self::all(), $values), JSON_PRETTY_PRINT));
    }
}
