<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Contracts;

interface ContentProvider
{
    public function getContent(): string;
}
