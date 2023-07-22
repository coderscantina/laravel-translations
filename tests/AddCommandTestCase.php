<?php namespace CodersCantina\Translations;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use CodersCantina\Translations\Commands\AddTranslation;
use Symfony\Component\Console\Tester\CommandTester;

class AddCommandTestCase extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function itAddsATranslation()
    {
        $this->addTranslation([
            'key'   => 'foo',
            'value' => 'bar'
        ]);

        $translation = Translation::where('key', 'foo')->first();
        $this->assertEquals('bar', $translation->value);
        $this->assertEquals('en', $translation->language_iso);
    }

    /** @test */
    public function itAddATranslationWithAGivenLanguage()
    {
        $this->addTranslation([
            'key'    => 'foo',
            'value'  => 'bar',
            '--lang' => 'baz',
        ]);

        $translation = Translation::where('key', 'foo')->first();
        $this->assertEquals('bar', $translation->value);
        $this->assertEquals('baz', $translation->language_iso);
    }

    /**
     * @param $args
     *
     * @return CommandTester
     */
    protected function addTranslation($args): CommandTester
    {
        $command = $this->app->make(AddTranslation::class);
        $command->setLaravel($this->app);
        $commandTester = new CommandTester($command);
        $commandTester->execute($args);

        return $commandTester;
    }
}
