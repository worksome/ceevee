<?php

namespace Worksome\Ceevee\Providers;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Worksome\Ceevee\Ceevee;
use Worksome\Ceevee\Commands\CeeveeCommand;

class CeeveeServiceProvider extends PackageServiceProvider
{
    public function packageRegistered(): void
    {
        $this->app->bind('ceevee', Ceevee::class);
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->setBasePath(__DIR__ . '/../')
            ->name('ceevee')
            ->hasConfigFile()
            ->hasCommand(CeeveeCommand::class);
    }
}
