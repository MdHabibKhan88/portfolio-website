<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\HandlesFields;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use HandlesFields;

    protected function section(string $section): array
    {
        $def = config('admin.profile_sections.'.$section);
        abort_unless($def, 404);

        return $def;
    }

    public function edit(string $section)
    {
        return view('admin.profile', [
            'section' => $section, 'def' => $this->section($section), 'model' => Profile::current(),
        ]);
    }

    public function update(Request $request, string $section)
    {
        $def = $this->section($section);
        $profile = Profile::current();

        $validated = $request->validate($this->buildRules($def['fields']));
        $data = $this->collectData($request, $def['fields'], $validated, $profile, $def['dir']);

        $profile->fill($data)->save();
        Activity::log('Updated '.$section);

        return redirect()->route('admin.profile.edit', $section)->with('success', t('a_saved'));
    }
}
