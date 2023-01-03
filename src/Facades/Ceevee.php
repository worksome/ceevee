<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Facades;

use Illuminate\Support\Facades\Facade;
use Symfony\Component\HttpFoundation\File\File;
use Worksome\Ceevee\Contracts\ContentProvider;
use Worksome\Ceevee\Managers\ParserManager;
use Worksome\Ceevee\Parsers\NullParser;
use Worksome\Ceevee\Support\CVDetail;

/**
 * @method static CVDetail read(ContentProvider|File|string $file) Read the given resume and return relevant results.
 * @method static void     assertRead(int|null $times = null)      Ensure that the `read` method has been called at least a given number of times. Note that you need to call `fake` on the facade first.
 *
 * @see \Worksome\Ceevee\Ceevee
 */
final class Ceevee extends Facade
{
    /**
     * Bind a fake parser into the container for testing
     * purposes. You may optionally pass any number
     * of CVDetails to return in sequenced order.
     */
    public static function fake(CVDetail ...$fakeDetails): void
    {
        /** @var \Worksome\Ceevee\Ceevee $instance */
        $instance = self::$app->make(\Worksome\Ceevee\Ceevee::class);

        /** @var ParserManager $manager */
        $manager = self::$app->make(ParserManager::class);

        /** @var NullParser $nullParser */
        $nullParser = $manager->driver('null');
        $nullParser->fakeSequence($fakeDetails);

        $instance->usingParser($nullParser);

        self::swap($instance);
    }

    protected static function getFacadeAccessor(): string
    {
        return \Worksome\Ceevee\Ceevee::class;
    }
}
