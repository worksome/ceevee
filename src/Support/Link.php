<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Support;

final class Link
{
    public function __construct(
        private string $name,
        private string $url,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
