<?php

namespace CodersCantina\Translations;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Config;

class TranslationsTest extends TestCase
{
    use LazilyRefreshDatabase;

    const TRANSLATIONS = [
        'en' => ['labels.foo' => 'Foo', 'labels.bar' => 'Bar, {name}'],
        'de' => ['labels.foo' => 'Fuu'],
        'fr' => ['labels.baz' => 'Baz'],
    ];

    public function setUp(): void
    {
        parent::setUp();
        Config::set('translations.replaceRegex', '/\{([^}]*)\}/');
    }

    /** @test */
    public function itHandlesTranslations()
    {
        $this->createTranslations(self::TRANSLATIONS);

        $this->assertEquals('Foo', __('labels.foo'));
        $this->assertEquals('Fuu', __('labels.foo', [], 'de'));
        $this->assertEquals('Foo', __('labels.foo', [], 'fr'));
        $this->assertEquals('Baz', __('labels.baz', [], 'fr'));

        $this->assertEquals('Bar, John', __('labels.bar', ['name' => 'John']));

        $this->assertEquals('non.existing.key', __('non.existing.key'));
        $this->assertEquals('key with normal text', __('key with normal text'));
        $this->assertEquals(':strange key with normal text', __(':strange key with normal text'));
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
