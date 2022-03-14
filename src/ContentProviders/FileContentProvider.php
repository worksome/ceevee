<?php

declare(strict_types=1);

namespace Worksome\Ceevee\ContentProviders;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Worksome\Ceevee\Contracts\ContentProvider;

final class FileContentProvider implements ContentProvider
{
    public function __construct(private File $file)
    {
    }

    /**
     * @throws FileException
     */
    public function getContent(): string
    {
        return $this->file->getContent();
    }
}
