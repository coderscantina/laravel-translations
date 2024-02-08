<?php

namespace CodersCantina\Translations;

use DB;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Support\Collection;

class DatabaseTranslationLoader implements Loader
{
    protected string $defaultLocale;

    protected array $fallbacks;

    protected string $table;

    public function __construct(string $defaultLocale, array $fallbacks)
    {
        $this->defaultLocale = $defaultLocale;
        $this->fallbacks = $fallbacks;
        $cls = config('translations.model', Translation::class);
        $this->table = (new $cls)->getTable();
    }

    /** @inheritDoc */
    public function load($locale, $group, $namespace = null): array
    {
        $stack = $this->getLocaleStack($locale);
        if ($namespace == '*') {
            return $this->fetch($stack);
        }

        return $this->fetch($stack, $group, $namespace);
    }

    public function addNamespace($namespace, $hint): void
    {
    }

    public function addJsonPath($path): void
    {
    }

    public function namespaces(): array
    {
        return [];
    }

    protected function fetch(Collection $stack, ?string $group = null, ?string $namespace = null): array
    {
        $shouldLoadWholeGroup = $group == '*.*' || is_null($group);

        $strings = $stack
            ->map(fn($locale) => $shouldLoadWholeGroup ?
                $this->fetchTranslationsForLocale($locale) :
                $this->fetchTranslationsByKey($namespace, $group, $locale))
            ->reduce(fn($a, $b) => $a + $b, []);

        return collect($strings)
            ->mapWithKeys(fn($item) => [$this->translationKey($item) => $item->value])
            ->toArray();
    }

    protected function fetchTranslationsForLocale(string $locale): array
    {
        return DB::table($this->table)
            ->where('language_iso', $locale)
            ->get(['namespace', 'key', 'value'])
            ->keyBy(fn($item) => $this->translationKey($item))
            ->toArray();
    }

    protected function fetchTranslationsByKey(?string $namespace, string $key, string $locale = null): array
    {
        $query = DB::table($this->table)
            ->where(
                function ($query) use ($key) {
                    $query->where('key', $key)
                        ->orWhere('key', 'LIKE', "$key.%");
                }
            )->where('namespace', $namespace);

        if ($locale) {
            $query->where('language_iso', $locale);
        }

        return $query->get(['namespace', 'key', 'value'])
            ->toArray();
    }

    protected function getLocaleStack(string $targetLocale, ?array $fallbacks = null): Collection
    {
        if (!$fallbacks) {
            $fallbacks = explode(',', config('app.fallback_locale'));
        }

        $fallbacks = array_filter($fallbacks, fn($item) => $item != $targetLocale);

        return collect([$targetLocale, ...array_reverse($fallbacks)]);
    }

    protected function translationKey($item): string
    {
        if ($item->namespace && $item->namespace != '*') {
            return $item->namespace . '::' . $item->key;
        }

        return $item->key;
    }
}
