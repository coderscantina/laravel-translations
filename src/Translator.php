<?php

namespace CodersCantina\Translations;

use Illuminate\Support\Arr;
use Illuminate\Translation\Translator as BaseTranslator;

class Translator extends BaseTranslator
{
    /** @inheritDoc */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $locale = $locale ?: $this->locale;
        [$namespace, $group, $item] = $this->parseKey($key);
        $this->load($namespace, $group, $locale);

        $line = $this->loaded[$namespace][$group][$locale][$key] ?? null;

        if (!isset($line)) {
            $locales = $fallback ? $this->localeArray($locale) : [$locale];

            foreach ($locales as $locale) {
                if (!is_null(
                    $line = $this->getLine(
                        $namespace,
                        $group,
                        $locale,
                        $item,
                        $replace
                    )
                )) {
                    return $line;
                }
            }
        }

        return $this->makeReplacements($line ?: $key, $replace);
    }

    public function parseKey($key)
    {
        if (!str_contains($key, '.')) {
            return [null, null, $key];
        }

        return parent::parseKey($key);
    }

    protected function makeReplacements($translation, array $replacements)
    {
        return preg_replace_callback(
            config('translations.replaceRegex'),
            function ($match) use ($replacements) {
                return Arr::get($replacements, $match[1]);
            },
            // BaseTranslator#makeReplacements uses strtr which requires the passed array to only contain strings
            parent::makeReplacements($translation, array_filter($replacements, 'is_string'))
        );
    }
}
