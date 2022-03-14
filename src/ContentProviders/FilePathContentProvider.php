<?php

declare(strict_types=1);

namespace Worksome\Ceevee\ContentProviders;

use Symfony\Component\HttpFoundation\File\File;
use Worksome\Ceevee\Contracts\ContentProvider;

final class FilePathContentProvider implements ContentProvider
{
    public function __construct(private string $filePath)
    {
    }

    public function getContent(): string
    {
        return (new File($this->filePath))->getContent();
    }
}
