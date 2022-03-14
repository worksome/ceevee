<?php

declare(strict_types=1);

namespace Worksome\Ceevee\Support;

final class CVDetail
{
    public function __construct(
        private ?int $yearsOfExperience = null,
        private ?string $summary = null,
        private mixed $rawResponse = null,
    ) {
    }

    public function yearsOfExperience(): ?int
    {
        return $this->yearsOfExperience;
    }

    public function summary(): ?string
    {
        return $this->summary;
    }

    public function fullResponse(): mixed
    {
        return $this->rawResponse;
    }
}
