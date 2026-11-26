<?php

namespace App\Concerns;

trait HasTranslations
{
    /** Returns the field in the current language, falling back to English. */
    public function tr(string $field): ?string
    {
        $value = $this->{$field.'_'.app()->getLocale()} ?? null;

        return filled($value) ? $value : ($this->{$field.'_en'} ?? null);
    }
}
