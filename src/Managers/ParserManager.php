<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Managers;

use Illuminate\Support\Manager;
use Worksome\Ceevee\Parsers\NullParser;
use Worksome\Ceevee\Parsers\SovrenParser;

final class ParserManager extends Manager
{
    public function getDefaultDriver(): string
    {
        // @phpstan-ignore-next-line
        return $this->config->get('CEEVEE_DRIVER') ?? 'null';
    }

    public function createNullDriver(): NullParser
    {
        return new NullParser();
    }

    public function createSovrenDriver(): SovrenParser
    {
        return new SovrenParser();
    }
}
