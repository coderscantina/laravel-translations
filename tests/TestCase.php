<?php namespace CodersCantina\Translations;

use GrahamCampbell\TestBench\AbstractPackageTestCase;

class TestCase extends AbstractPackageTestCase
{
    protected function getServiceProviderClass()
    {
        return ServiceProvider::class;
    }
}
