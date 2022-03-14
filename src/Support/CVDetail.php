<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Support;

final class CVDetail
{
    public function __construct(
        private ?int $yearsOfExperience = null,
    ) {
    }

    public function yearsOfExperience(): ?int
    {
        return $this->yearsOfExperience;
    }
}
