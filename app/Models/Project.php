<?php

namespace App\Models;

use Illuminate\Support\Str;

class Project extends PortfolioModel
{
    protected $table = 'projects';

    protected static function booted(): void
    {
        static::saving(function (Project $project) {
            if (blank($project->slug)) {
                $base = Str::slug((string) $project->title_en) ?: 'project';
                $slug = $base;
                $i = 2;
                while (static::where('slug', $slug)->where('id', '!=', $project->id ?? 0)->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $project->slug = $slug;
            }
        });
    }

    /** @return string[] */
    public function techList(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', (string) $this->technologies))));
    }
}
