<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Validation + upload handling for the field definitions in config/admin.php.
 */
trait HandlesFields
{
    /** Expands bilingual fields: returns [column => type]. */
    protected function columns(array $fields): array
    {
        $cols = [];
        foreach ($fields as $f) {
            $type = $f['type'] ?? 'text';
            if (! empty($f['bilingual'])) {
                $cols[$f['name'].'_en'] = $type;
                $cols[$f['name'].'_bn'] = $type;
            } else {
                $cols[$f['name']] = $type;
            }
        }

        return $cols;
    }

    protected function selectOptions(array $fields): array
    {
        $options = [];
        foreach ($fields as $f) {
            if (($f['type'] ?? '') === 'select' && ($f['options'] ?? null) === 'skill_categories') {
                $options[$f['name']] = \App\Models\SkillCategory::ordered()->get()
                    ->mapWithKeys(fn ($c) => [$c->id => $c->tr('name')])->all();
            }
        }

        return $options;
    }

    protected function buildRules(array $fields, ?string $table = null, ?Model $model = null): array
    {
        $options = $this->selectOptions($fields);
        $rules = [];

        foreach ($fields as $f) {
            $type = $f['type'] ?? 'text';
            $columns = ! empty($f['bilingual'])
                ? [$f['name'].'_en' => true, $f['name'].'_bn' => false]
                : [$f['name'] => true];

            foreach ($columns as $col => $primary) {
                $r = [(($f['required'] ?? false) && $primary) ? 'required' : 'nullable'];

                switch ($type) {
                    case 'textarea':
                        $r[] = 'string';
                        $r[] = 'max:'.($f['max'] ?? 10000);
                        break;
                    case 'email':
                        $r[] = 'email';
                        $r[] = 'max:150';
                        break;
                    case 'url':
                        $r[] = 'url';
                        $r[] = 'max:500';
                        break;
                    case 'date':
                        $r[] = 'date';
                        break;
                    case 'number':
                        $r[] = 'integer';
                        $r[] = 'min:'.($f['min'] ?? 0);
                        $r[] = 'max:'.($f['max'] ?? 100);
                        break;
                    case 'select':
                        $r[] = Rule::in(array_keys($options[$col] ?? []));
                        break;
                    case 'boolean':
                        $r = ['boolean'];
                        break;
                    case 'image':
                        array_push($r, 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096');
                        break;
                    case 'file':
                        array_push($r, 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:8192');
                        break;
                    case 'document':
                        array_push($r, 'file', 'mimes:pdf,doc,docx', 'max:5120');
                        break;
                    default:
                        $r[] = 'string';
                        $r[] = 'max:'.($f['max'] ?? 255);
                }

                if ($table && ! empty($f['unique']) && $primary) {
                    $r[] = Rule::unique($table, $col)->ignore($model?->getKey());
                }
                if ($table && ! empty($f['unique_scope'])) {
                    $r[] = Rule::unique($table, $col)
                        ->where($f['unique_scope'], request($f['unique_scope']))
                        ->ignore($model?->getKey());
                }

                $rules[$col] = $r;
            }
        }

        return $rules;
    }

    /** Builds the attribute array for the model, storing/removing uploaded files as needed. */
    protected function collectData(Request $request, array $fields, array $validated, ?Model $model, string $dir): array
    {
        $data = [];

        foreach ($this->columns($fields) as $col => $type) {
            if (in_array($type, ['image', 'file', 'document'], true)) {
                $old = $model?->{$col};
                if ($request->hasFile($col)) {
                    $this->deleteFile($old);
                    // store() generates a random safe filename; the client's name is never used.
                    $data[$col] = $request->file($col)->store($dir, 'public');
                } elseif ($request->boolean('remove_'.$col)) {
                    $this->deleteFile($old);
                    $data[$col] = null;
                }
            } elseif ($type === 'boolean') {
                $data[$col] = $request->boolean($col);
            } else {
                $data[$col] = $validated[$col] ?? null;
            }
        }

        return $data;
    }

    protected function deleteFile(?string $path): void
    {
        if (filled($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    protected function deleteFilesOf(array $fields, Model $model): void
    {
        foreach ($this->columns($fields) as $col => $type) {
            if (in_array($type, ['image', 'file', 'document'], true)) {
                $this->deleteFile($model->{$col});
            }
        }
    }
}
