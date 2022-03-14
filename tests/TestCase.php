<?php

namespace Worksome\Ceevee\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Worksome\Ceevee\Providers\CeeveeServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            CeeveeServiceProvider::class,
        ];
    }
}
