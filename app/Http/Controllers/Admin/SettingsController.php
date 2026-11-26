<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\HandlesFields;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Fluent;

class SettingsController extends Controller
{
    use HandlesFields;

    public function edit()
    {
        $values = [];
        foreach (Setting::all() as $s) {
            $values[$s->key.'_en'] = $s->value_en;
            $values[$s->key.'_bn'] = $s->value_bn;
        }

        return view('admin.settings', [
            'fields' => config('admin.settings_fields'),
            'model' => new Fluent($values),
        ]);
    }

    public function update(Request $request)
    {
        $fields = config('admin.settings_fields');
        $validated = $request->validate($this->buildRules($fields));

        foreach ($fields as $f) {
            Setting::updateOrCreate(['key' => $f['name']], [
                'value_en' => $validated[$f['name'].'_en'] ?? null,
                'value_bn' => $validated[$f['name'].'_bn'] ?? null,
            ]);
        }
        Cache::forget('site_settings');
        Activity::log('Updated website settings');

        return redirect()->route('admin.settings.edit')->with('success', t('a_saved'));
    }
}
