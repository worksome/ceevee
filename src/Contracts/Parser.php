<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Contracts;

use Worksome\Ceevee\Support\CVDetail;

interface Parser
{
    public function parse(string $content): CVDetail;
}
