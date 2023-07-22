<?php

namespace CodersCantina\Translations\Commands;

use CodersCantina\Translations\Translation;
use Illuminate\Console\Command;

class AddTranslation extends Command
{
    protected $signature = 'translations:add {key} {value?} {--L|lang=en}';

    protected $description = 'Add a new translation to the database.';

    public function handle(): int
    {
        $key = $this->argument('key');

        $value = count($this->arguments()) == 2
            ? $this->argument('value')
            : $this->ask("Enter the value for '{$key}'");

        $translation = Translation::create([
                                               'key' => $key,
                                               'value' => $value,
                                               'language_iso' => $this->option('lang'),
                                           ]);

        $this->info("Translation '{$translation['key']}' created successfully.");

        return 0;
    }
}
