<?php namespace CodersCantina\Translations;

use Illuminate\Translation\TranslationServiceProvider;
use CodersCantina\Translations\Commands\AddTranslation;

class ServiceProvider extends TranslationServiceProvider
{
    protected $defer = true;

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../migrations/');
    }

    public function register()
    {
        $this->commands([
            AddTranslation::class
        ]);

        $this->mergeConfigFrom(__DIR__ . '/../config/translations.php', 'translations');

        $this->app->singleton('translator', function ($app) {
            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
            $locale = $app['config']['app.locale'];
            $fallback = $app['config']['app.fallback_locale'];

            $loader = new DatabaseTranslationLoader($locale, explode(',', $fallback));
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
