<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Testing;

use Worksome\Ceevee\Support\CVDetail;

final class CVDetailFactory
{
    private int $yearsOfExperience = 0;

    public static function new(): self
    {
        return new self();
    }

    public function withYearsOfExperience(int $years): self
    {
        $this->yearsOfExperience = $years;

        return $this;
    }

    public function create(): CVDetail
    {
        return new CVDetail(
            $this->yearsOfExperience,
        );
    }
}
