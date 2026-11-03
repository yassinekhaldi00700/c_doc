<?php

namespace App\Support;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Admin-editable site settings (the application deadline shown in the
 * announcement bar and the oral exam window for professors' picks). Kept in storage/app/site-settings.json rather than a
 * new database column, so the schema stays unchanged.
 */
class SiteSettings
{
    private const FILE = 'site-settings.json';

    private const DEFAULTS = [
        'application_deadline' => '2026-10-31',
        'oral_exam_min_date' => '2026-11-09',
        'oral_exam_max_date' => '2026-11-13',
    ];

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

    /** First day (Y-m-d) professors may propose for an oral exam. */
    public static function oralExamMinDate(): string
    {
        return self::all()['oral_exam_min_date'];
    }

    /** Last day (Y-m-d, inclusive) professors may propose for an oral exam. */
    public static function oralExamMaxDate(): string
    {
        return self::all()['oral_exam_max_date'];
    }

    public static function update(array $values): void
    {
        Storage::disk('local')->put(self::FILE, json_encode(array_merge(self::all(), $values), JSON_PRETTY_PRINT));
    }
}
