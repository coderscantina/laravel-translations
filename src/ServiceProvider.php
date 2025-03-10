<?php namespace CodersCantina\Translations;

use CodersCantina\Translations\Commands\AddTranslation;
use Illuminate\Translation\TranslationServiceProvider;

class ServiceProvider extends TranslationServiceProvider
{
    protected $defer = true;

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../migrations/');

        $this->publishes([
            __DIR__ . '/../config/translations.php' => config_path('translations.php'),
        ], 'config');
    }


    public function register()
    {
        $this->registerCommands();
        $this->registerConfig();
        $this->registerTranslator();
    }

    /**
     * Register the commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->commands([
            Commands\AddTranslation::class
        ]);
    }

    /**
     * Register the configuration.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/translations.php', 'translations');
    }

    /**
     * Register the translator.
     *
     * @return void
     */
    protected function registerTranslator()
    {
        $this->app->singleton('translator', function ($app) {
            $locale = $app['config']['app.locale'];
            $fallback = $app['config']['app.fallback_locale'];
            $cacheDuration = $app['config']['translations.cache_duration'] ?? 3600;

            $loader = new DatabaseTranslationLoader(
                $locale,
                explode(',', $fallback),
                $cacheDuration
            );

            $trans = new Translator($loader, $locale);
            $trans->setFallback($fallback);

            return $trans;
        });
    }

    public function provides()
    {
        return [
            AddTranslation::class,
            'translator'
        ];
    }
}
