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
        /** @var array{account_id: string, service_key: string, region: string|null, options: array<mixed>|null} $options */
        $options = $this->config->get('ceevee.services.sovren');

        /** @var Factory $factory */
        $factory = $this->container->make(Factory::class);

        return new SovrenParser(
            $factory,
            $options['account_id'],
            $options['service_key'],
            $options['region'] ?? 'eu',
            $options['options'] ?? [],
        );
    }
}
