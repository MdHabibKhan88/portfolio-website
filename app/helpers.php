<?php

use App\Models\Setting;
use App\Models\Translation;
use Illuminate\Support\Facades\Cache;

if (! function_exists('t')) {
    /** Translate a UI string; admin-edited overrides (Language Content) win over lang files. */
    function t(string $key): string
    {
        $overrides = Cache::remember('ui_overrides', 3600, function () {
            try {
                return Translation::all()->mapWithKeys(fn ($r) => [$r->key => ['en' => $r->en, 'bn' => $r->bn]])->all();
            } catch (\Throwable $e) {
                return [];
            }
        });

        $value = $overrides[$key][app()->getLocale()] ?? null;

        return filled($value) ? $value : __('ui.'.$key);
    }
}

if (! function_exists('setting')) {
    /** Read a website setting in the current language (falls back to English, then $default). */
    function setting(string $key, ?string $default = null): ?string
    {
        $all = Cache::remember('site_settings', 3600, function () {
            try {
                return Setting::all()->mapWithKeys(fn ($r) => [$r->key => ['en' => $r->value_en, 'bn' => $r->value_bn]])->all();
            } catch (\Throwable $e) {
                return [];
            }
        });

        $value = $all[$key][app()->getLocale()] ?? null;
        if (blank($value)) {
            $value = $all[$key]['en'] ?? null;
        }

        return filled($value) ? $value : $default;
    }
}

if (! function_exists('file_url')) {
    /** Public URL of a file stored on the "public" disk, or null. */
    function file_url(?string $path): ?string
    {
        return filled($path) ? asset('storage/'.ltrim($path, '/')) : null;
    }
}
