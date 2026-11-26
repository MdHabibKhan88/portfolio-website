<?php

namespace App\Http\Controllers\Admin;

use App\Concerns\HandlesFields;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    use HandlesFields;

    protected function def(string $resource): array
    {
        $def = config('admin.resources.'.$resource);
        abort_unless($def, 404);

        return $def;
    }

    public function index(string $resource)
    {
        $def = $this->def($resource);
        $items = $def['model']::ordered()->get();

        return view('admin.resource.index', compact('resource', 'def', 'items'));
    }

    public function create(string $resource)
    {
        $def = $this->def($resource);

        return view('admin.resource.form', [
            'resource' => $resource, 'def' => $def, 'model' => new $def['model'],
            'options' => $this->selectOptions($def['fields']),
        ]);
    }

    public function store(Request $request, string $resource)
    {
        $def = $this->def($resource);
        $model = new $def['model'];
        $this->persist($request, $def, $model, $resource, true);

        return redirect()->route('admin.resource.index', $resource)->with('success', t('a_saved'));
    }

    public function edit(string $resource, int $id)
    {
        $def = $this->def($resource);

        return view('admin.resource.form', [
            'resource' => $resource, 'def' => $def, 'model' => $def['model']::findOrFail($id),
            'options' => $this->selectOptions($def['fields']),
        ]);
    }

    public function update(Request $request, string $resource, int $id)
    {
        $def = $this->def($resource);
        $model = $def['model']::findOrFail($id);
        $this->persist($request, $def, $model, $resource, false);

        return redirect()->route('admin.resource.index', $resource)->with('success', t('a_saved'));
    }

    public function destroy(string $resource, int $id)
    {
        $def = $this->def($resource);
        $model = $def['model']::findOrFail($id);
        $label = $model->{$def['title']};
        $this->deleteFilesOf($def['fields'], $model);
        $model->delete();
        Activity::log('Deleted '.$resource.': '.$label);

        return redirect()->route('admin.resource.index', $resource)->with('success', t('a_deleted'));
    }

    public function toggle(string $resource, int $id)
    {
        $def = $this->def($resource);
        $model = $def['model']::findOrFail($id);
        $model->update(['is_active' => ! $model->is_active]);

        return back()->with('success', t('a_saved'));
    }

    public function move(string $resource, int $id, string $direction)
    {
        abort_unless(in_array($direction, ['up', 'down'], true), 404);
        $class = $this->def($resource)['model'];

        $ids = $class::ordered()->pluck('id')->all();
        $i = array_search($id, $ids);
        $j = $direction === 'up' ? $i - 1 : $i + 1;

        if ($i !== false && isset($ids[$j])) {
            [$ids[$i], $ids[$j]] = [$ids[$j], $ids[$i]];
            foreach ($ids as $pos => $rowId) {
                $class::whereKey($rowId)->update(['sort_order' => $pos + 1]);
            }
        }

        return back();
    }

    protected function persist(Request $request, array $def, $model, string $resource, bool $creating): void
    {
        $rules = $this->buildRules($def['fields'], $def['table'], $model);
        $rules['is_active'] = ['boolean'];
        $validated = $request->validate($rules);

        $data = $this->collectData($request, $def['fields'], $validated, $creating ? null : $model, $def['dir'] ?? $resource);
        $data['is_active'] = $request->boolean('is_active');

        if ($creating) {
            $data['sort_order'] = ((int) $def['model']::max('sort_order')) + 1;
        }

        $model->fill($data)->save();
        Activity::log(($creating ? 'Created ' : 'Updated ').$resource.': '.$model->{$def['title']});
    }
}
