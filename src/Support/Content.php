<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Support;

use Illuminate\Support\Traits\Macroable;
use Symfony\Component\HttpFoundation\File\File;
use Worksome\Ceevee\ContentProviders\FileContentProvider;
use Worksome\Ceevee\ContentProviders\FilePathContentProvider;
use Worksome\Ceevee\ContentProviders\RawContentProvider;

final class Content
{
    use Macroable;

    public static function fromRaw(string $content): RawContentProvider
    {
        return new RawContentProvider($content);
    }

    public static function fromFile(File $file): FileContentProvider
    {
        return new FileContentProvider($file);
    }

    public static function fromPath(string $filePath): FilePathContentProvider
    {
        return new FilePathContentProvider($filePath);
    }
}
