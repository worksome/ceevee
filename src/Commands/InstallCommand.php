<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Commands;

use Illuminate\Console\Command;

final class InstallCommand extends Command
{
    protected $signature = 'ceevee:install';

    protected $description = 'Install Ceevee\'s configuration file in your project.';

    public function handle(): int
    {
        $this->call('vendor:publish', [
            '--tag' => 'ceevee',
        ]);

        return self::SUCCESS;
    }
}
