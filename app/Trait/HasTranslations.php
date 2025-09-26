<?php

namespace App\Trait;

use App\Models\Translation;

trait HasTranslations
{
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * Get a translation value.
     */
    public function getTranslation(string $key, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();

        return $this->translations
            ->where('locale', $locale)
            ->where('key', $key)
            ->value('value');
    }

    /**
     * Set / update a translation.
     */
    public function setTranslation(string $key, string $locale, string $value): void
    {
        $this->translations()->updateOrCreate(
            ['locale' => $locale, 'key' => $key],
            ['value' => $value],
        );
    }

    /**
     * Bulk set translations for a given locale.
     *
     * @param array $data ['key' => 'value', ...]
     */
    public function setTranslations(array $data, string $locale): void
    {
        foreach ($data as $key => $value) {
            $this->setTranslation($key, $locale, $value);
        }
    }
}
