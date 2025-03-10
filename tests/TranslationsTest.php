<?php

namespace CodersCantina\Translations;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class TranslationsTest extends TestCase
{
    use LazilyRefreshDatabase;

    const TRANSLATIONS = [
        'en' => ['labels.foo' => 'Foo', 'labels.bar' => 'Bar, {name}', 'common.close' => 'Close'],
        'de' => ['labels.foo' => 'Fuu', 'common.close' => 'Schließen'],
        'fr' => ['labels.baz' => 'Baz', 'common.close' => 'Fermer'],
    ];

    public function setUp(): void
    {
        parent::setUp();
        Config::set('translations.replaceRegex', '/\{([^}]*)\}/');
    }

    /** @test */
    public function it_handles_basic_translations()
    {
        $this->createTranslations(self::TRANSLATIONS);

        $this->assertEquals('Foo', __('labels.foo'));
        $this->assertEquals('Fuu', __('labels.foo', [], 'de'));
        $this->assertEquals('Foo', __('labels.foo', [], 'fr')); // Falls back to English
        $this->assertEquals('Baz', __('labels.baz', [], 'fr'));
    }

    /** @test */
    public function it_handles_replacements_in_translations()
    {
        $this->createTranslations(self::TRANSLATIONS);

        $this->assertEquals('Bar, John', __('labels.bar', ['name' => 'John']));
        $this->assertEquals('Bar, {name}', __('labels.bar')); // No replacement provided
    }

    /** @test */
    public function it_handles_missing_translations()
    {
        $this->createTranslations(self::TRANSLATIONS);

        $this->assertEquals('non.existing.key', __('non.existing.key'));
        $this->assertEquals('key with normal text', __('key with normal text'));
        $this->assertEquals(':strange key with normal text', __(':strange key with normal text'));
    }

    /** @test */
    public function it_respects_language_fallbacks()
    {
        $this->createTranslations(self::TRANSLATIONS);

        Config::set('app.fallback_locale', 'fr');

        // Should fall back to French for missing English translation
        $this->assertEquals('Baz', __('labels.baz'));

        // Should fall back to English for missing Italian translation
        Config::set('app.fallback_locale', 'en');
        $this->assertEquals('Foo', __('labels.foo', [], 'it'));
    }

    /** @test */
    public function it_handles_non_string_replacements()
    {
        $this->createTranslations([
            'en' => ['count' => 'You have {count} items'],
        ]);

        $this->assertEquals('You have 5 items', __('count', ['count' => 5]));
        $this->assertEquals('You have  items', __('count', ['count' => null])); // Null is filtered out
        $this->assertEquals('You have 1 items', __('count', ['count' => true]));
    }

    /** @test */
    public function it_handles_soft_deletes()
    {
        $this->createTranslations([
            'en' => ['test.key' => 'Test Value'],
        ]);

        Translation::where('key', 'test.key')->delete();

        $this->assertEquals('test.key', __('test.key'));
    }

    /** @test */
    public function it_loads_translations_from_database()
    {
        Translation::create([
            'key' => 'test.key',
            'value' => 'Test Value',
            'language_iso' => 'en',
        ]);

        $loader = $this->app->make('translator')->getLoader();
        $translations = $loader->load('en', '*', '*');

        $this->assertArrayHasKey('test.key', $translations);
        $this->assertEquals('Test Value', $translations['test.key']);
    }

    /** @test */
    public function it_respects_locale_stack()
    {
        Translation::create([
            'key' => 'test.key',
            'value' => 'English Value',
            'language_iso' => 'en',
        ]);

        Translation::create([
            'key' => 'test.key',
            'value' => 'French Value',
            'language_iso' => 'fr',
        ]);

        // Set English as fallback
        Config::set('app.fallback_locale', 'en');

        $loader = $this->app->make('translator')->getLoader();

        // Should get French value for French locale
        $translations = $loader->load('fr', '*', '*');
        $this->assertEquals('French Value', $translations['test.key']);

        // Should get English value for German locale (fallback)
        $translations = $loader->load('de', '*', '*');
        $this->assertEquals('English Value', $translations['test.key']);
    }

    protected function createTranslations(array $collection, ?string $namespace = '*'): void
    {
        foreach ($collection as $lang => $translations) {
            foreach ($translations as $key => $value) {
                Translation::forceCreate([
                    'key' => $key,
                    'value' => $value,
                    'language_iso' => $lang,
                    'namespace' => $namespace,
                ]);
            }
        }
    }

}
