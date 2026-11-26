<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TranslationController extends Controller
{
    public function edit()
    {
        return view('admin.translations', [
            'en' => require lang_path('en/ui.php'),
            'bn' => require lang_path('bn/ui.php'),
            'overrides' => Translation::all()->keyBy('key'),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'en' => ['nullable', 'array'], 'en.*' => ['nullable', 'string', 'max:1000'],
            'bn' => ['nullable', 'array'], 'bn.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $known = array_keys(require lang_path('en/ui.php'));

        foreach ($known as $key) {
            $en = $request->input("en.$key");
            $bn = $request->input("bn.$key");

            if (blank($en) && blank($bn)) {
                Translation::where('key', $key)->delete();
            } else {
                Translation::updateOrCreate(['key' => $key], ['en' => $en, 'bn' => $bn]);
            }
        }

        Cache::forget('ui_overrides');
        Activity::log('Updated language content');

        return redirect()->route('admin.translations.edit')->with('success', t('a_saved'));
    }
}
