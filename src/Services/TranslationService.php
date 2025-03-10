<?php

namespace CodersCantina\Translations\Services;

use CodersCantina\Translations\Translation;
use Illuminate\Support\Arr;

class TranslationService
{
    /**
     * Get translations for multiple namespaces.
     *
     * @param array $namespaces
     * @param string $language
     * @return array
     */
    public function getTranslationsForNamespaces(array $namespaces, string $language): array
    {
        $result = [];
        $result = $this->applyLanguage(config('app.fallback_language'), $namespaces, $result);
        if ($language !== config('app.fallback_language')) {
            $result = $this->applyLanguage($language, $namespaces, $result);
        }

        return $result;
    }

    /**
     * Apply translations for a specific language.
     *
     * @param string $language
     * @param array $namespaces
     * @param array $result
     * @return array
     */
    protected function applyLanguage(string $language, array $namespaces, array $result): array
    {
        $class = config('translations.model', Translation::class);

        $class::where('language_iso', $language)
            ->whereIn('namespace', $namespaces)
            ->each(function ($t) use (&$result) {
                Arr::set($result[$t->namespace], $t->key, $t->value);
            });

        return $result;
    }

    /**
     * Find missing translations across languages.
     *
     * @param array $languages
     * @param string|null $namespace
     * @return array
     */
    public function findMissingTranslations(array $languages, ?string $namespace = '*'): array
    {
        $class = config('translations.model', Translation::class);
        $missing = [];

        // Get all keys from primary language
        $primaryLanguage = $languages[0];
        $allKeys = $class::where('language_iso', $primaryLanguage)
            ->where('namespace', $namespace)
            ->pluck('key')
            ->toArray();

        // Check each other language for missing keys
        foreach (array_slice($languages, 1) as $language) {
            $existingKeys = $class::where('language_iso', $language)
                ->where('namespace', $namespace)
                ->pluck('key')
                ->toArray();

            $missingKeys = array_diff($allKeys, $existingKeys);

            if (!empty($missingKeys)) {
                $missing[$language] = $missingKeys;
            }
        }

        return $missing;
    }
}
