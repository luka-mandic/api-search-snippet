<?php

namespace Comprigo\Compare\Tests;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../../../../bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        $app['cache']->setDefaultDriver('array');
        $app->setLocale('en');
        return $app;
    }

    public function createMock($originalClassName)
    {
        return parent::createMock($originalClassName);
    }
}
