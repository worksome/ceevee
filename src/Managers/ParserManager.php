<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Managers;

use Illuminate\Http\Client\Factory;
use Illuminate\Support\Manager;
use Worksome\Ceevee\Parsers\NullParser;
use Worksome\Ceevee\Parsers\SovrenParser;

final class ParserManager extends Manager
{
    public function getDefaultDriver(): string
    {
        // @phpstan-ignore-next-line
        return $this->config->get('ceevee.default') ?? 'null';
    }

    public function createNullDriver(): NullParser
    {
        return new NullParser();
    }

    public function createSovrenDriver(): SovrenParser
    {
        $options = $this->config->get('ceevee.services.sovren');

        return new SovrenParser(
            $this->container->make(Factory::class),
            $options['account_id'],
            $options['service_key'],
            $options['region'] ?? 'eu',
            $options['options'] ?? [],
        );
    }
}
