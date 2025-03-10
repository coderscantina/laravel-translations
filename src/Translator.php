<?php

namespace CodersCantina\Translations;

use Illuminate\Support\Arr;
use Illuminate\Translation\Translator as BaseTranslator;

class Translator extends BaseTranslator
{
    /**
     * Get the translation for the given key.
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @param bool $fallback
     * @return string|array
     */
    public function get($key, array $replace = [], $locale = null, $fallback = true)
    {
        $locale = $locale ?: $this->locale;
        [$namespace, $group, $item] = $this->parseKey($key);

        try {
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
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Translation error: {$e->getMessage()}");
            return $key; // Fallback to key if any error occurs
        }
    }

    /**
     * Parse a key into namespace, group, and item.
     *
     * @param string $key
     * @return array
     */
    public function parseKey($key)
    {
        if (!str_contains($key, '.')) {
            return [null, null, $key];
        }

        return parent::parseKey($key);
    }

    /**
     * Make the place-holder replacements on a line.
     *
     * @param string $line
     * @param array $replace
     * @return string
     */
    public function makeReplacements($line, array $replace)
    {
        if (!is_string($line) || empty($replace)) {
            return $line;
        }

        $replaceRegex = config('translations.replaceRegex', '/\{([^}]*)\}/');

        return preg_replace_callback(
            $replaceRegex,
            function ($match) use ($replace) {
                return Arr::get($replace, $match[1], $match[0]);
            },
            // BaseTranslator#makeReplacements uses strtr which requires the passed array to only contain strings
            parent::makeReplacements($line, array_filter($replace, 'is_string'))
        );
    }
}
