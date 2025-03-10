<?php namespace CodersCantina\Translations;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function itBuildsAnInstance()
    {
        $trans = $this->app->make('translator');
        $this->assertTrue($trans instanceof Translator);
    }

    /** @test */
    public function it_registers_the_config()
    {
        $this->assertNotNull(config('translations'));
        $this->assertNotNull(config('translations.replaceRegex'));
    }
}
