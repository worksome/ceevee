<?php

namespace Worksome\Ceevee\Commands;

use Illuminate\Console\Command;

class CeeveeCommand extends Command
{
    public $signature = 'ceevee';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
