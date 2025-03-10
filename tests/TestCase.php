<?php namespace CodersCantina\Translations;

use GrahamCampbell\TestBench\AbstractPackageTestCase;

class TestCase extends AbstractPackageTestCase
{
    protected static function getServiceProviderClass(): string
    {
        return ServiceProvider::class;
    }
}
