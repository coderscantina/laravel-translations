<?php

namespace CodersCantina\Translations;

use DB;
use Illuminate\Contracts\Translation\Loader;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class DatabaseTranslationLoader implements Loader
{
    /**
     * The default locale for the application.
     *
     * @var string
     */
    protected string $defaultLocale;

    /**
     * The fallback locales for the application.
     *
     * @var array
     */
    protected array $fallbacks;

    /**
     * The database table used for translations.
     *
     * @var string
     */
    protected string $table;

    public function __construct(string $defaultLocale, array $fallbacks)
    {
        $this->defaultLocale = $defaultLocale;
        $this->fallbacks = $fallbacks;
        $cls = config('translations.model', Translation::class);
        $this->table = (new $cls)->getTable();
    }

    /**
     * Load the specified language group.
     *
     * @param string $locale
     * @param string $group
     * @param string|null $namespace
     * @return array
     */
    public function load($locale, $group, $namespace = null): array
    {
        $stack = $this->getLocaleStack($locale);
        if ($namespace == '*') {
            return $this->fetch($stack);
        }

        return $this->fetch($stack, $group, $namespace);
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param string $namespace
     * @param string $hint
     * @return void
     */
    public function addNamespace($namespace, $hint): void
    {
        // Not needed for database translations
    }

    /**
     * Add a new JSON path to the loader.
     *
     * @param string $path
     * @return void
     */
    public function addJsonPath($path): void
    {
        // Not needed for database translations
    }

    /**
     * Get an array of all the registered namespaces.
     *
     * @return array
     */
    public function namespaces(): array
    {
        return [];
    }

    /**
     * Fetch translations from the database.
     *
     * @param Collection $stack
     * @param string|null $group
     * @param string|null $namespace
     * @return array
     */
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

    /**
     * Fetch all translations for a locale.
     *
     * @param string $locale
     * @return array
     */
    protected function fetchTranslationsForLocale(string $locale): array
    {
        return \Illuminate\Support\Facades\DB::table($this->table)
            ->where('language_iso', $locale)
            ->whereNull('deleted_at')
            ->get(['namespace', 'key', 'value'])
            ->keyBy(fn($item) => $this->translationKey($item))
            ->toArray();
    }

    /**
     * Fetch translations by key and namespace.
     *
     * @param string|null $namespace
     * @param string $key
     * @param string|null $locale
     * @return array
     */
    protected function fetchTranslationsByKey(?string $namespace, string $key, string $locale = null): array
    {
        $query = \Illuminate\Support\Facades\DB::table($this->table)
            ->whereNull('deleted_at')
            ->where(function (Builder $query) use ($key) {
                // Use bound parameters to prevent SQL injection
                $query->where('key', $key)
                    ->orWhere('key', 'LIKE', \Illuminate\Support\Facades\DB::raw('CONCAT(?, \'%\')', [$key . '.']));
            })
            ->where('namespace', $namespace);

        if ($locale) {
            $query->where('language_iso', $locale);
        }

        return $query->get(['namespace', 'key', 'value'])
            ->toArray();
    }

    /**
     * Get the locale stack to search for translations.
     *
     * @param string $targetLocale
     * @param array|null $fallbacks
     * @return Collection
     */
    protected function getLocaleStack(string $targetLocale, ?array $fallbacks = null): Collection
    {
        if (!$fallbacks) {
            $fallbacks = explode(',', config('app.fallback_locale'));
        }

        $fallbacks = array_filter($fallbacks, fn($item) => $item != $targetLocale);

        return collect([$targetLocale, ...array_reverse($fallbacks)]);
    }

    /**
     * Get the translation key.
     *
     * @param object $item
     * @return string
     */
    protected function translationKey($item): string
    {
        if ($item->namespace && $item->namespace != '*') {
            return $item->namespace . '::' . $item->key;
        }

        return $item->key;
    }
}
