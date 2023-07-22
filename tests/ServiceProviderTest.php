<?php namespace CodersCantina\Translations;

class ServiceProviderTest extends TestCase
{
    /** @test */
    public function itBuildsAnInstance()
    {
        $trans = $this->app->make('translator');
        $this->assertTrue($trans instanceof Translator);
    }
}
