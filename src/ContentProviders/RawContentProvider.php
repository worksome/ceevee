<?php

declare(strict_types=1);

namespace Worksome\Ceevee\ContentProviders;

use Worksome\Ceevee\Contracts\ContentProvider;

final class RawContentProvider implements ContentProvider
{
    public function __construct(private string $rawContent)
    {
    }

    public function getContent(): string
    {
        return $this->rawContent;
    }
}
