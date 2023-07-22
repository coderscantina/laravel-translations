<?php

namespace CodersCantina\Translations\Services;

use Illuminate\Support\Arr;

class TranslationService
{
    public function getTranslationsForNamespaces($namespaces, $language): array
    {
        $result = [];
        $result = $this->applyLanguage(config('app.fallback_language'), $namespaces, $result);
        if ($language !== config('app.fallback_language')) {
            $result = $this->applyLanguage($language, $namespaces, $result);
        }

        return $result;
    }

    protected function applyLanguage(string $language, array $namespaces, array $result): array
    {
        $class = config('translation.model');

        $class::where('language_iso', $language)
            ->whereIn('namespace', $namespaces)
            ->each(function ($t) use (&$result) {
                Arr::set($result[$t->namespace], $t->key, $t->value);
            });

        return $result;
    }
}
